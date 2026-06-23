<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-theme-from/10 text-theme-from shrink-0">
                <x-icon name="currency-dollar" class="w-5 h-5" />
            </div>
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-widest font-semibold">Billing › Payments</p>
                <h1 class="text-xl font-bold text-gray-800">Record Payment</h1>
            </div>
        </div>
    </x-slot>

    <div class="p-6">
        <div class="max-w-2xl mx-auto">
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">

                <div class="bg-gradient-to-r from-theme-from to-theme-to px-8 py-6">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center shadow-inner">
                            <x-icon name="currency-dollar" class="w-7 h-7 text-white" />
                        </div>
                        <div>
                            <h2 class="text-white text-xl font-bold">Record Payment</h2>
                            @if($selectedInvoice)
                                <p class="text-sky-100 text-sm mt-0.5">
                                    Invoice {{ $selectedInvoice->invoice_number }} —
                                    Remaining: ${{ number_format($selectedInvoice->remaining_amount, 2) }}
                                </p>
                            @else
                                <p class="text-sky-100 text-sm mt-0.5">Select an invoice and enter payment details</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="px-8 py-7">
                    @php
                        $inputClass = fn(string $field) =>
                            'w-full px-4 py-2.5 rounded-xl border text-gray-800 text-sm placeholder-gray-400 ' .
                            'transition-all duration-200 ' .
                            'focus:outline-none focus:ring-2 focus:ring-theme-from/40 focus:border-theme-from ' .
                            ($errors->has($field)
                                ? 'border-red-400 bg-red-50 ring-1 ring-red-300'
                                : 'border-gray-200 bg-gray-50 hover:border-theme-from');
                        $selectStyle = "background-image: url('data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' fill=\'none\' viewBox=\'0 0 24 24\' stroke=\'%236b7280\'%3E%3Cpath stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M19 9l-7 7-7-7\'/%3E%3C/svg%3E'); background-position: right 12px center; background-size: 16px;";
                    @endphp

                    <form action="{{ route('payments.store') }}" method="POST" class="space-y-5">
                        @csrf

                        {{-- Invoice Selection --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-1.5">
                                <span class="flex items-center gap-1.5">
                                    <x-icon name="document" class="w-4 h-4 text-green-500" />
                                    Invoice <span class="text-red-400">*</span>
                                </span>
                            </label>
                            @if($selectedInvoice)
                                <div class="px-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50 text-sm font-semibold text-gray-800">
                                    {{ $selectedInvoice->invoice_number }} — {{ $selectedInvoice->patient->full_name ?? '—' }}
                                </div>
                                <input type="hidden" name="invoice_id" value="{{ $selectedInvoice->id }}">
                            @else
                                <select name="invoice_id" required class="{{ $inputClass('invoice_id') }} appearance-none bg-no-repeat cursor-pointer" style="{{ $selectStyle }}">
                                    <option value="">— Select Invoice —</option>
                                    @foreach($invoices as $inv)
                                        <option value="{{ $inv->id }}" {{ old('invoice_id', $selectedInvoiceId) == $inv->id ? 'selected' : '' }}>
                                            {{ $inv->invoice_number }} — {{ $inv->patient->full_name ?? '—' }}
                                            (${{ number_format($inv->remaining_amount, 2) }} remaining)
                                        </option>
                                    @endforeach
                                </select>
                            @endif
                            <x-input-error :messages="$errors->get('invoice_id')" class="mt-1.5" />
                        </div>

                        {{-- Amount & Date --}}
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="amount" class="block text-sm font-semibold text-gray-600 mb-1.5">
                                    Amount ($) <span class="text-red-400">*</span>
                                </label>
                                <input type="number" id="amount" name="amount" step="0.01" min="0.01"
                                    value="{{ old('amount', $selectedInvoice?->remaining_amount) }}"
                                    class="{{ $inputClass('amount') }}">
                                <x-input-error :messages="$errors->get('amount')" class="mt-1.5" />
                            </div>
                            <div>
                                <label for="paid_at" class="block text-sm font-semibold text-gray-600 mb-1.5">
                                    Payment Date <span class="text-red-400">*</span>
                                </label>
                                <input type="date" id="paid_at" name="paid_at"
                                    value="{{ old('paid_at', now()->format('Y-m-d')) }}"
                                    class="{{ $inputClass('paid_at') }}">
                                <x-input-error :messages="$errors->get('paid_at')" class="mt-1.5" />
                            </div>
                        </div>

                        {{-- Payment Method --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-1.5">
                                Payment Method <span class="text-red-400">*</span>
                            </label>
                            <div class="grid grid-cols-3 gap-2">
                                @foreach(['cash' => '💵 Cash', 'card' => '💳 Card', 'bank_transfer' => '🏦 Transfer'] as $val => $label)
                                    <label class="relative cursor-pointer">
                                        <input type="radio" name="payment_method" value="{{ $val }}" class="peer sr-only"
                                            {{ old('payment_method', 'cash') === $val ? 'checked' : '' }}>
                                        <div class="px-4 py-3 rounded-xl border-2 border-gray-200 text-center text-sm font-semibold text-gray-600 peer-checked:border-theme-from peer-checked:bg-theme-from/10 peer-checked:text-theme-from transition-all hover:border-theme-from/50">
                                            {{ $label }}
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                            <x-input-error :messages="$errors->get('payment_method')" class="mt-1.5" />
                        </div>

                        {{-- Currency --}}
                        <input type="hidden" name="currency_id" value="{{ $currencies->first()?->id }}">

                        {{-- Notes --}}
                        <div>
                            <label for="notes" class="block text-sm font-semibold text-gray-600 mb-1.5">Notes (Optional)</label>
                            <textarea id="notes" name="notes" rows="2"
                                placeholder="Payment reference, notes…"
                                class="{{ $inputClass('notes') }} resize-none">{{ old('notes') }}</textarea>
                        </div>

                        <div class="border-t border-dashed border-gray-200 pt-2"></div>
                        <div class="flex items-center justify-end gap-3">
                            <x-button-cancel/>
                            <button type="submit"
                                class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-theme-from to-theme-to text-white text-sm font-bold shadow-lg hover:shadow-xl hover:-translate-y-0.5 active:scale-95 transition-all flex items-center gap-2">
                                <x-icon name="check-circle" class="w-4 h-4" />
                                Record Payment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
