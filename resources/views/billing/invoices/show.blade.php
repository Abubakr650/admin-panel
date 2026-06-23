<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between w-full gap-4">
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-widest font-semibold">Billing › Invoices</p>
                <h1 class="text-xl font-bold text-gray-800">Invoice #{{ $invoice->invoice_number }}</h1>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('payments.create', ['invoice_id' => $invoice->id]) }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-green-50 text-green-700 border border-green-200 rounded-xl text-sm font-semibold hover:bg-green-100 transition-all">
                    <x-icon name="plus" class="w-4 h-4" /> Add Payment
                </a>
                <a href="{{ route('invoices.edit', $invoice) }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 rounded-xl text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-all shadow-sm">
                    <x-icon name="pencil" class="w-4 h-4 text-theme-from" /> Edit
                </a>
                <form action="{{ route('invoices.destroy', $invoice) }}" method="POST" onsubmit="return confirm('Archive this invoice?')">
                    @csrf @method('DELETE')
                    <x-button-archive>Archive</x-button-archive>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="p-6">
        <div class="max-w-3xl mx-auto space-y-6">
            @php
                $statusColors = [
                    'unpaid'  => 'bg-red-50 text-red-600 border-red-100',
                    'partial' => 'bg-amber-50 text-amber-600 border-amber-100',
                    'paid'    => 'bg-green-50 text-green-600 border-green-100',
                ];
            @endphp

            {{-- Header Card --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-theme-from to-theme-to px-8 py-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sky-100 text-xs font-semibold uppercase tracking-wider">Invoice</p>
                            <h2 class="text-white text-2xl font-extrabold">#{{ $invoice->invoice_number }}</h2>
                            <p class="text-sky-100 text-sm mt-1">{{ $invoice->created_at->format('F d, Y') }}</p>
                        </div>
                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-bold border {{ $statusColors[$invoice->payment_status] ?? 'bg-gray-50 text-gray-600 border-gray-100' }}">
                            {{ ucfirst($invoice->payment_status) }}
                        </span>
                    </div>
                </div>

                <div class="px-8 py-5 grid grid-cols-2 gap-6">
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Patient</p>
                        <a href="{{ route('patients.show', $invoice->patient_id) }}" class="font-bold text-gray-800 hover:text-theme-from transition-colors">
                            {{ $invoice->patient->full_name ?? '—' }}
                        </a>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Doctor</p>
                        <p class="font-bold text-gray-800">Dr. {{ $invoice->doctor->user->name ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Currency</p>
                        <p class="font-semibold text-gray-800">{{ $invoice->currency->code ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Created</p>
                        <p class="font-semibold text-gray-800">{{ $invoice->created_at->format('M d, Y h:i A') }}</p>
                    </div>
                </div>
            </div>

            {{-- Treatment Items --}}
            @if($invoice->items && $invoice->items->count() > 0)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-8 py-4 border-b border-gray-100">
                    <h3 class="font-bold text-gray-800 flex items-center gap-2">
                        <x-icon name="clipboard" class="w-5 h-5 text-theme-from" /> Treatment Items
                    </h3>
                </div>
                <table class="min-w-full">
                    <thead>
                        <tr class="bg-gray-50/70">
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Treatment</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Qty</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Unit</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($invoice->items as $item)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-3 text-sm text-gray-700">
                                    {{ $item->treatment->service->name ?? $item->treatment->description ?? 'Treatment' }}
                                    <p class="text-xs text-gray-400">{{ ucfirst(str_replace('_', ' ', $item->treatment->type ?? '')) }}</p>
                                </td>
                                <td class="px-6 py-3 text-center text-sm text-gray-600">{{ $item->quantity }}</td>
                                <td class="px-6 py-3 text-right text-sm text-gray-600">${{ number_format($item->unit_price, 2) }}</td>
                                <td class="px-6 py-3 text-right font-bold text-gray-800">${{ number_format($item->total_price, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif

            {{-- Financial Summary --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-8 py-5 space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Subtotal</span>
                        <span class="font-semibold text-gray-800">${{ number_format($invoice->total_amount, 2) }}</span>
                    </div>
                    @if($invoice->discount_percent > 0)
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Discount ({{ $invoice->discount_percent }}%)</span>
                            <span class="font-semibold text-red-500">-${{ number_format($invoice->total_amount * $invoice->discount_percent / 100, 2) }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between text-sm pt-2 border-t border-dashed border-gray-200">
                        <span class="font-bold text-gray-800">Final Amount</span>
                        <span class="text-2xl font-extrabold text-theme-from">${{ number_format($invoice->final_amount, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm pt-2 border-t border-dashed border-gray-200">
                        <span class="text-gray-500">Total Paid</span>
                        <span class="font-semibold text-green-600">${{ number_format($invoice->total_paid, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="font-bold text-gray-800">Remaining</span>
                        <span class="font-extrabold {{ $invoice->remaining_amount > 0 ? 'text-red-500' : 'text-green-600' }}">
                            ${{ number_format($invoice->remaining_amount, 2) }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Payments --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-8 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="font-bold text-gray-800 flex items-center gap-2">
                        <x-icon name="currency" class="w-5 h-5 text-theme-from" /> Payment History
                    </h3>
                    @if($invoice->remaining_amount > 0)
                        <a href="{{ route('payments.create', ['invoice_id' => $invoice->id]) }}"
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-green-50 text-green-700 text-xs font-semibold border border-green-200 hover:bg-green-100 transition-all">
                            <x-icon name="plus" class="w-3.5 h-3.5" /> Add Payment
                        </a>
                    @endif
                </div>
                @if($invoice->payments && $invoice->payments->count() > 0)
                    <div class="divide-y divide-gray-50">
                        @foreach($invoice->payments as $payment)
                            <div class="px-8 py-4 flex items-center justify-between">
                                <div>
                                    <p class="font-semibold text-gray-800">${{ number_format($payment->amount, 2) }}</p>
                                    <p class="text-xs text-gray-400">{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }} · {{ \Carbon\Carbon::parse($payment->paid_at)->format('M d, Y') }}</p>
                                    @if($payment->notes)
                                        <p class="text-xs text-gray-400 italic mt-0.5">{{ $payment->notes }}</p>
                                    @endif
                                </div>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-green-50 text-green-600 border border-green-100">
                                    Paid
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="px-8 py-6 text-center text-sm text-gray-400 italic">No payments recorded yet.</div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
