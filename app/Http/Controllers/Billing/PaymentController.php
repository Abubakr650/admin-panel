<?php

namespace App\Http\Controllers\Billing;

use App\Http\Controllers\Controller;
use App\Models\Billing\Currency;
use App\Models\Billing\Invoice;
use App\Models\Billing\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $isArchived = $request->input('archived') === 'true';

        $query = Payment::with(['invoice.patient', 'currency']);

        if ($isArchived) {
            $query->onlyTrashed()->orderBy('deleted_at', 'desc');
        } else {
            if ($request->filled('search')) {
                $term = $request->input('search');
                $query->where(function ($q) use ($term) {
                    $q->whereHas('invoice', fn($iq) =>
                        $iq->where('invoice_number', 'LIKE', '%' . $term . '%')
                           ->orWhereHas('patient', fn($pq) =>
                               $pq->where('full_name', 'LIKE', '%' . $term . '%')
                           )
                    )->orWhere('payment_method', 'LIKE', '%' . $term . '%');
                });
            }
            if ($request->filled('method') && $request->input('method') !== 'all') {
                $query->where('payment_method', $request->input('method'));
            }
            $query->latest();
        }

        $payments = $query->paginate(15)->onEachSide(1)->withQueryString();
        $archivedCount = Payment::onlyTrashed()->count();
        $totalCollected = Payment::whereNull('deleted_at')->sum('amount');

        return view('billing.payments.index', compact('payments', 'archivedCount', 'totalCollected'));
    }

    /**
     * Show the form for creating a new resource.
     * Supports ?invoice_id= to pre-select an invoice.
     */
    public function create(Request $request)
    {
        $invoices   = Invoice::with('patient')
            ->whereIn('payment_status', [Invoice::STATUS_UNPAID, Invoice::STATUS_PARTIAL])
            ->get();
        $currencies = Currency::all();
        $selectedInvoiceId = $request->input('invoice_id');

        $selectedInvoice = null;
        if ($selectedInvoiceId) {
            $selectedInvoice = Invoice::with('patient')->find($selectedInvoiceId);
        }

        return view('billing.payments.create', compact(
            'invoices', 'currencies', 'selectedInvoiceId', 'selectedInvoice'
        ));
    }

    /**
     * Store a newly created resource in storage.
     * Updates invoice payment_status after saving.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'invoice_id'     => 'required|exists:invoices,id',
            'amount'         => 'required|numeric|min:0.01',
            'payment_method' => 'required|string',
            'currency_id'    => 'required|exists:currencies,id',
            'paid_at'        => 'required|date',
            'notes'          => 'nullable|string',
        ]);

        $payment = Payment::create([
            'invoice_id'     => $validated['invoice_id'],
            'amount'         => $validated['amount'],
            'payment_method' => $validated['payment_method'],
            'currency_id'    => $validated['currency_id'],
            'paid_at'        => $validated['paid_at'],
            'exchange_rate'  => 1.0,
            'notes'          => $validated['notes'] ?? null,
            'created_by'     => auth()->id(),
        ]);

        // Update invoice payment status
        $this->syncInvoiceStatus($payment->invoice_id);

        return redirect()->route('invoices.show', $payment->invoice_id)
            ->with('success', 'Payment of $' . number_format($validated['amount'], 2) . ' recorded successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $payment = Payment::with(['invoice.patient', 'invoice.doctor.user', 'currency'])
            ->findOrFail($id);
        return view('billing.payments.show', compact('payment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $payment    = Payment::findOrFail($id);
        $invoices   = Invoice::with('patient')->get();
        $currencies = Currency::all();
        return view('billing.payments.edit', compact('payment', 'invoices', 'currencies'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'amount'         => 'required|numeric|min:0.01',
            'payment_method' => 'required|string',
            'paid_at'        => 'required|date',
            'notes'          => 'nullable|string',
        ]);

        $payment = Payment::findOrFail($id);
        $payment->update([
            'amount'         => $validated['amount'],
            'payment_method' => $validated['payment_method'],
            'paid_at'        => $validated['paid_at'],
            'notes'          => $validated['notes'] ?? null,
            'updated_by'     => auth()->id(),
        ]);

        $this->syncInvoiceStatus($payment->invoice_id);

        return redirect()->route('payments.show', $payment->id)
            ->with('success', 'Payment updated successfully!');
    }

    /**
     * Remove the specified resource from storage (soft delete).
     */
    public function destroy(string $id)
    {
        $payment = Payment::withTrashed()->findOrFail($id);
        $invoiceId = $payment->invoice_id;
        $payment->delete();

        // Re-sync invoice status after removing payment
        $this->syncInvoiceStatus($invoiceId);

        $previousUrl = URL::previous();
        if (str_contains($previousUrl, route('payments.show', $payment->id))) {
            return redirect()->route('payments.index')->with('success', 'Payment archived!');
        }
        return redirect()->back()->with('success', 'Payment archived!');
    }

    /**
     * Restore a soft-deleted payment.
     */
    public function restore(string $id)
    {
        $payment = Payment::withTrashed()->findOrFail($id);
        $payment->restore();
        $this->syncInvoiceStatus($payment->invoice_id);
        return redirect()->route('payments.index', ['archived' => 'true'])
            ->with('success', 'Payment restored successfully!');
    }

    /**
     * Sync invoice payment_status based on total paid vs final_amount.
     */
    private function syncInvoiceStatus(string $invoiceId): void
    {
        $invoice   = Invoice::find($invoiceId);
        if (!$invoice) return;

        $totalPaid = Payment::where('invoice_id', $invoiceId)->whereNull('deleted_at')->sum('amount');
        $final     = (float) $invoice->final_amount;

        if ($totalPaid <= 0) {
            $status = Invoice::STATUS_UNPAID;
        } elseif ($totalPaid >= $final) {
            $status = Invoice::STATUS_PAID;
        } else {
            $status = Invoice::STATUS_PARTIAL;
        }

        $invoice->update(['payment_status' => $status]);
    }
}
