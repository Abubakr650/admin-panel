<?php

namespace App\Http\Controllers\Pharmacy;

use App\Http\Controllers\Controller;
use App\Models\Billing\Currency;
use App\Models\Billing\Invoice;
use App\Models\Billing\InvoiceItem;
use App\Models\Billing\Payment;
use App\Models\Clinic\Doctor;
use App\Models\Clinic\Patient;
use App\Models\Clinic\Treatment;
use App\Models\Pharmacy\PharmacyBatch;
use Illuminate\Http\Request;

class PharmacyDispenseController extends Controller
{
    /**
     * Show the pharmacy dispense (sales) page.
     */
    public function index(Request $request)
    {
        $patients   = Patient::orderBy('full_name')->get();
        $doctors    = Doctor::where('is_active', true)->with('user')->get()->sortBy(fn($d) => $d->user->name ?? '');
        $currencies = Currency::all();

        // Walk-in patient is now anonymous (null)
        $walkinPatient = null;

        // Build query for available batches with filters
        $query = PharmacyBatch::with(['pharmacyItem', 'supplier'])
            ->where('remaining_quantity', '>', 0)
            ->where('expiry_date', '>=', now());

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('batch_number', 'like', "%{$search}%")
                  ->orWhereHas('pharmacyItem', function ($qi) use ($search) {
                      $qi->where('commercial_name', 'like', "%{$search}%")
                         ->orWhere('scientific_name', 'like', "%{$search}%")
                         ->orWhere('qr_code', $search);
                  });
            });
        }

        if ($category = $request->input('category')) {
            $query->whereHas('pharmacyItem', fn($q) => $q->where('category', $category));
        }

        if ($form = $request->input('form')) {
            $query->whereHas('pharmacyItem', fn($q) => $q->where('form', $form));
        }

        $batches = $query->orderBy('expiry_date')->paginate(10)->appends($request->query());

        $totalAvailable = PharmacyBatch::where('remaining_quantity', '>', 0)
            ->where('expiry_date', '>=', now())->count();

        return view('pharmacy.dispense', compact(
            'patients', 'doctors', 'batches', 'currencies', 'walkinPatient', 'totalAvailable'
        ));
    }

    /**
     * AJAX: Return unpaid/partial invoices for a patient.
     */
    public function patientInvoices(Patient $patient)
    {
        $invoices = Invoice::where('patient_id', $patient->id)
            ->whereIn('payment_status', ['unpaid', 'partial'])
            ->orderByDesc('created_at')
            ->get()
            ->map(fn($inv) => [
                'id'               => $inv->id,
                'invoice_number'   => $inv->invoice_number,
                'total'            => (float) $inv->final_amount,
                'paid'             => (float) $inv->total_paid,
                'remaining'        => (float) $inv->remaining_amount,
                'status'           => $inv->payment_status,
                'created_at'       => $inv->created_at->format('M d, Y'),
            ]);

        return response()->json($invoices);
    }

    /**
     * Process a pharmacy sale (dispense).
     *
     * Modes:
     *  1. New invoice  — mode = 'new'  (default, creates fresh invoice + full/partial payment)
     *  2. Existing inv — mode = 'existing', invoice_id required (adds items + records payment)
     *
     * In both modes, amount_paid controls how much is recorded as payment now.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id'       => 'nullable|exists:patients,id',
            'doctor_id'        => 'nullable|exists:doctors,id',
            'currency_id'      => 'required|exists:currencies,id',
            'items'            => 'required|array|min:1',
            'items.*.batch_id' => 'required|exists:pharmacy_batches,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price'    => 'required|numeric|min:0',
            'payment_method'   => 'required|string',
            'notes'            => 'nullable|string',
            'mode'             => 'nullable|in:new,existing',
            'invoice_id'       => 'nullable|exists:invoices,id',
            'amount_paid'      => 'nullable|numeric|min:0',
        ]);

        $mode      = $validated['mode'] ?? 'new';
        $treatments  = [];
        $itemsTotal  = 0;

        // ── 1. Create treatments & decrement stock ─────────────────────────────
        foreach ($validated['items'] as $item) {
            $batch = PharmacyBatch::findOrFail($item['batch_id']);

            if ($batch->remaining_quantity < $item['quantity']) {
                return redirect()->back()->withInput()
                    ->with('error', 'Insufficient stock for ' . $batch->pharmacyItem->commercial_name .
                           '. Available: ' . $batch->remaining_quantity);
            }

            $itemTotal   = $item['price'] * $item['quantity'];
            $itemsTotal += $itemTotal;

            $treatment = Treatment::create([
                'patient_id'        => $validated['patient_id'] ?? null,
                'doctor_id'         => $validated['doctor_id'] ?? null,
                'pharmacy_batch_id' => $batch->id,
                'type'              => Treatment::TYPE_PHARMACY_DISPENSE,
                'quantity'          => $item['quantity'],
                'price'             => $item['price'],
                'discount'          => 0,
                'total'             => $itemTotal,
                'status'            => Treatment::STATUS_COMPLETED,
                'billing_status'    => Treatment::BILLING_BILLED,
                'description'       => 'Dispensed: ' . $batch->pharmacyItem->commercial_name,
                'created_by'        => auth()->id(),
            ]);

            $batch->decrement('remaining_quantity', $item['quantity']);
            $treatments[] = $treatment;
        }

        // ── 2a. NEW invoice mode ───────────────────────────────────────────────
        if ($mode === 'new' || empty($validated['invoice_id'])) {
            $invoiceCount = Invoice::count() + 1;
            $invoice = Invoice::create([
                'invoice_number'   => 'PH-' . str_pad($invoiceCount, 5, '0', STR_PAD_LEFT),
                'patient_id'       => $validated['patient_id'] ?? null,
                'doctor_id'        => $validated['doctor_id'] ?? null,
                'total_amount'     => $itemsTotal,
                'discount_percent' => 0,
                'final_amount'     => $itemsTotal,
                'payment_status'   => 'unpaid',
                'exchange_rate'    => 1.0,
                'currency_id'      => $validated['currency_id'],
                'created_by'       => auth()->id(),
            ]);

            foreach ($treatments as $treatment) {
                InvoiceItem::create([
                    'invoice_id'   => $invoice->id,
                    'treatment_id' => $treatment->id,
                    'quantity'     => $treatment->quantity,
                    'unit_price'   => $treatment->price,
                    'total_price'  => $treatment->total,
                ]);
            }

            $amountPaid = min((float)($validated['amount_paid'] ?? $itemsTotal), $itemsTotal);
        }
        // ── 2b. EXISTING invoice mode ──────────────────────────────────────────
        else {
            $invoice = Invoice::findOrFail($validated['invoice_id']);

            foreach ($treatments as $treatment) {
                InvoiceItem::create([
                    'invoice_id'   => $invoice->id,
                    'treatment_id' => $treatment->id,
                    'quantity'     => $treatment->quantity,
                    'unit_price'   => $treatment->price,
                    'total_price'  => $treatment->total,
                ]);
            }

            // Update invoice totals
            $newTotalAmount = $invoice->total_amount + $itemsTotal;
            $newFinalAmount = $newTotalAmount * (1 - ($invoice->discount_percent / 100));

            $invoice->update([
                'total_amount'  => $newTotalAmount,
                'final_amount'  => $newFinalAmount,
            ]);

            $maxPayable = clone $invoice; // To get fresh remaining
            $amountPaid = min((float)($validated['amount_paid'] ?? 0), $maxPayable->refresh()->remaining_amount);
        }

        // ── 3. Record payment if amount_paid > 0 ──────────────────────────────
        if ($amountPaid > 0) {
            Payment::create([
                'invoice_id'     => $invoice->id,
                'amount'         => $amountPaid,
                'payment_method' => $validated['payment_method'],
                'paid_at'        => now(),
                'exchange_rate'  => 1.0,
                'currency_id'    => $validated['currency_id'],
                'notes'          => $validated['notes'] ?? 'Pharmacy sale',
                'created_by'     => auth()->id(),
            ]);
        }

        // ── 4. Recalculate invoice payment_status ─────────────────────────────
        $totalPaid = (float) $invoice->payments()->sum('amount');
        $finalAmt  = (float) $invoice->final_amount;

        $invoice->update([
            'payment_status' => match(true) {
                $totalPaid <= 0            => 'unpaid',
                $totalPaid >= $finalAmt   => 'paid',
                default                   => 'partial',
            }
        ]);

        $msg = 'Sale completed! Invoice: ' . $invoice->invoice_number;
        if ($amountPaid > 0) {
            $msg .= ' — Paid: $' . number_format($amountPaid, 2);
            $remaining = $invoice->fresh()->remaining_amount;
            if ($remaining > 0) {
                $msg .= ' — Remaining: $' . number_format($remaining, 2);
            }
        }

        return redirect()->route('pharmacy.dispense')->with('success', $msg);
    }
}
