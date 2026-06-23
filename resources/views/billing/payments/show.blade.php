<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between w-full gap-4">
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-widest font-semibold">Billing › Payments</p>
                <h1 class="text-xl font-bold text-gray-800">Payment Details</h1>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('payments.edit', $payment) }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 rounded-xl text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-all shadow-sm">
                    <x-icon name="pencil" class="w-4 h-4 text-theme-from" /> Edit
                </a>
                <form action="{{ route('payments.destroy', $payment) }}" method="POST" onsubmit="return confirm('Archive this payment?')">
                    @csrf @method('DELETE')
                    <x-button-archive>Archive</x-button-archive>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="p-6">
        <div class="max-w-2xl mx-auto space-y-5">
            @php
                $methodIcons = ['cash' => '💵', 'card' => '💳', 'bank_transfer' => '🏦', 'other' => '💱'];
            @endphp

            {{-- Main Card --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-theme-from to-theme-to px-8 py-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sky-100 text-xs font-semibold uppercase tracking-wider">Payment</p>
                            <h2 class="text-white text-3xl font-extrabold">${{ number_format($payment->amount, 2) }}</h2>
                            <p class="text-sky-100 text-sm mt-1">
                                {{ $methodIcons[$payment->payment_method] ?? '💱' }}
                                {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}
                                · {{ $payment->paid_at?->format('M d, Y') }}
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-sky-100 text-xs">Invoice</p>
                            <a href="{{ route('invoices.show', $payment->invoice_id) }}"
                               class="text-white font-bold text-lg hover:underline">
                                {{ $payment->invoice->invoice_number ?? '—' }}
                            </a>
                        </div>
                    </div>
                </div>

                <div class="px-8 py-6 grid grid-cols-2 gap-5">
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Patient</p>
                        <p class="font-bold text-gray-800">{{ $payment->invoice->patient->full_name ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Doctor</p>
                        <p class="font-bold text-gray-800">Dr. {{ $payment->invoice->doctor->user->name ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Currency</p>
                        <p class="font-semibold text-gray-800">{{ $payment->currency->code ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Recorded</p>
                        <p class="font-semibold text-gray-800">{{ $payment->created_at->format('M d, Y h:i A') }}</p>
                    </div>
                    @if($payment->notes)
                        <div class="col-span-2">
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Notes</p>
                            <p class="text-gray-700 italic">{{ $payment->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Invoice Summary --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-8 py-4 border-b border-gray-100">
                    <h3 class="font-bold text-gray-800 flex items-center gap-2">
                        <x-icon name="document" class="w-5 h-5 text-theme-from" /> Invoice Summary
                    </h3>
                </div>
                <div class="px-8 py-5 space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Invoice Total</span>
                        <span class="font-semibold text-gray-800">${{ number_format($payment->invoice->final_amount ?? 0, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Total Paid</span>
                        <span class="font-semibold text-green-600">${{ number_format($payment->invoice->total_paid ?? 0, 2) }}</span>
                    </div>
                    <div class="border-t border-dashed border-gray-200 pt-3 flex justify-between">
                        <span class="font-bold text-gray-800">Remaining</span>
                        @php $remaining = $payment->invoice->remaining_amount ?? 0; @endphp
                        <span class="font-extrabold {{ $remaining > 0 ? 'text-red-500' : 'text-green-600' }}">
                            ${{ number_format($remaining, 2) }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between">
                <a href="{{ route('payments.index') }}" class="text-sm text-theme-from hover:underline flex items-center gap-1">
                    <x-icon name="arrow-left" class="w-4 h-4" /> Back to Payments
                </a>
                <a href="{{ route('invoices.show', $payment->invoice_id) }}"
                   class="text-sm text-gray-500 hover:text-theme-from flex items-center gap-1 transition-colors">
                    View Full Invoice <x-icon name="arrow-right" class="w-4 h-4" />
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
