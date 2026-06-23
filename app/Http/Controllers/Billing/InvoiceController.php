<?php

namespace App\Http\Controllers\Billing;

use App\Http\Controllers\Controller;
use App\Models\Billing\Currency;
use App\Models\Billing\Invoice;
use App\Models\Billing\InvoiceItem;
use App\Models\Clinic\Appointment;
use App\Models\Clinic\Doctor;
use App\Models\Clinic\Patient;
use App\Models\Clinic\Treatment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $isArchived = $request->input('archived') === 'true';

        $query = Invoice::with(['patient', 'doctor.user', 'currency']);

        if ($isArchived) {
            $query->onlyTrashed()->orderBy('deleted_at', 'desc');
        } else {
            if ($request->filled('search')) {
                $term = $request->input('search');
                $query->where(function ($q) use ($term) {
                    $q->where('invoice_number', 'LIKE', '%' . $term . '%')
                      ->orWhereHas('patient', fn($pq) => $pq->where('full_name', 'LIKE', '%' . $term . '%'));
                });
            }
            if ($request->filled('payment_status') && $request->input('payment_status') !== 'all') {
                $query->where('payment_status', $request->input('payment_status'));
            }
            $query->latest();
        }

        $invoices = $query->paginate(15)->onEachSide(1)->withQueryString();
        $archivedCount = Invoice::onlyTrashed()->count();
        $unpaidCount = Invoice::where('payment_status', 'unpaid')->count();
        $partialCount = Invoice::where('payment_status', 'partial')->count();
        $paidCount = Invoice::where('payment_status', 'paid')->count();

        return view('billing.invoices.index', compact('invoices', 'archivedCount', 'unpaidCount', 'partialCount', 'paidCount'));
    }

    /**
     * Show the form for creating a new resource.
     * Supports ?appointment_id= to auto-load treatments.
     */
    public function create(Request $request)
    {
        $patients   = Patient::orderBy('full_name')->get();
        $doctors    = Doctor::where('is_active', true)->with('user')->get()->sortBy(fn($d) => $d->user->name ?? '');
        $currencies = Currency::all();

        $appointment = null;
        $pendingTreatments = collect();
        $selectedPatientId = $request->input('patient_id');
        $selectedDoctorId  = null;

        // If coming from an appointment, pre-load pending treatments
        if ($request->filled('appointment_id')) {
            $appointment = Appointment::with(['patient', 'doctor.user', 'treatments' => function ($q) {
                $q->where('billing_status', Treatment::BILLING_PENDING);
            }, 'treatments.service'])->findOrFail($request->input('appointment_id'));

            $pendingTreatments = $appointment->treatments;
            $selectedPatientId = $appointment->patient_id;
            $selectedDoctorId  = $appointment->doctor_id;
        }

        // Also load standalone pending treatments for a patient
        if ($selectedPatientId && !$request->filled('appointment_id')) {
            $pendingTreatments = Treatment::with('service')
                ->where('patient_id', $selectedPatientId)
                ->where('billing_status', Treatment::BILLING_PENDING)
                ->get();
        }

        return view('billing.invoices.create', compact(
            'patients', 'doctors', 'currencies', 'appointment',
            'pendingTreatments', 'selectedPatientId', 'selectedDoctorId'
        ));
    }

    /**
     * Store a newly created resource in storage.
     * Creates invoice from selected treatments.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id'       => 'required|exists:patients,id',
            'doctor_id'        => 'required|exists:doctors,id',
            'currency_id'      => 'required|exists:currencies,id',
            'discount_percent' => 'nullable|integer|min:0|max:100',
            'treatment_ids'    => 'required|array|min:1',
            'treatment_ids.*'  => 'exists:treatments,id',
        ]);

        // Gather treatments
        $treatments = Treatment::whereIn('id', $validated['treatment_ids'])
            ->where('billing_status', Treatment::BILLING_PENDING)
            ->get();

        if ($treatments->isEmpty()) {
            return redirect()->back()->withInput()
                ->with('error', 'No pending treatments found for the selected items.');
        }

        $totalAmount     = $treatments->sum('total');
        $discountPercent = $validated['discount_percent'] ?? 0;
        $finalAmount     = $totalAmount * (1 - $discountPercent / 100);

        // Generate invoice number
        $invoiceCount = Invoice::withTrashed()->count() + 1;
        $invoiceNumber = 'INV-' . str_pad($invoiceCount, 5, '0', STR_PAD_LEFT);

        $invoice = Invoice::create([
            'invoice_number'   => $invoiceNumber,
            'patient_id'       => $validated['patient_id'],
            'doctor_id'        => $validated['doctor_id'],
            'total_amount'     => $totalAmount,
            'discount_percent' => $discountPercent,
            'final_amount'     => $finalAmount,
            'payment_status'   => Invoice::STATUS_UNPAID,
            'exchange_rate'    => 1.0,
            'currency_id'      => $validated['currency_id'],
            'created_by'       => auth()->id(),
        ]);

        // Create invoice items and update treatment billing status
        foreach ($treatments as $treatment) {
            InvoiceItem::create([
                'invoice_id'   => $invoice->id,
                'treatment_id' => $treatment->id,
                'quantity'     => $treatment->quantity,
                'unit_price'   => $treatment->price,
                'total_price'  => $treatment->total,
            ]);

            $treatment->update(['billing_status' => Treatment::BILLING_BILLED]);
        }

        return redirect()->route('invoices.show', $invoice->id)
            ->with('success', 'Invoice ' . $invoiceNumber . ' created — Total: $' . number_format($finalAmount, 2));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $invoice = Invoice::with(['patient', 'doctor.user', 'currency', 'items.treatment.service', 'payments'])
            ->findOrFail($id);
        return view('billing.invoices.show', compact('invoice'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $invoice    = Invoice::with('items')->findOrFail($id);
        $patients   = Patient::orderBy('full_name')->get();
        $doctors    = Doctor::with('user')->get()->sortBy(fn($d) => $d->user->name ?? '');
        $currencies = Currency::all();
        return view('billing.invoices.edit', compact('invoice', 'patients', 'doctors', 'currencies'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'discount_percent' => 'nullable|integer|min:0|max:100',
            'payment_status'   => 'required|string',
        ]);

        $invoice = Invoice::findOrFail($id);
        $discountPercent = $validated['discount_percent'] ?? $invoice->discount_percent;
        $finalAmount = $invoice->total_amount * (1 - $discountPercent / 100);

        $invoice->update([
            'discount_percent' => $discountPercent,
            'final_amount'     => $finalAmount,
            'payment_status'   => $validated['payment_status'],
            'updated_by'       => auth()->id(),
        ]);

        return redirect()->route('invoices.show', $invoice->id)
            ->with('success', 'Invoice updated successfully!');
    }

    /**
     * Remove the specified resource from storage (soft delete).
     */
    public function destroy(string $id)
    {
        $invoice = Invoice::withTrashed()->findOrFail($id);

        // Revert treatments to pending if deleting invoice
        if (!$invoice->trashed()) {
            foreach ($invoice->items as $item) {
                if ($item->treatment) {
                    $item->treatment->update(['billing_status' => Treatment::BILLING_PENDING]);
                }
            }
        }

        $invoice->delete();

        $previousUrl = URL::previous();
        if (str_contains($previousUrl, route('invoices.show', $invoice->id))) {
            return redirect()->route('invoices.index')->with('success', 'Invoice archived successfully!');
        }
        return redirect()->back()->with('success', 'Invoice archived successfully!');
    }

    /**
     * Restore a soft-deleted invoice.
     */
    public function restore(string $id)
    {
        $invoice = Invoice::withTrashed()->findOrFail($id);
        $invoice->restore();
        return redirect()->route('invoices.index', ['archived' => 'true'])
            ->with('success', 'Invoice restored successfully!');
    }
}
