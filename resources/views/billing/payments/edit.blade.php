<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-theme-from/10 text-theme-from shrink-0">
                <x-icon name="currency-dollar" class="w-5 h-5" />
            </div>
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-widest font-semibold">Billing › Payments</p>
                <h1 class="text-xl font-bold text-gray-800">Edit Payment</h1>
            </div>
        </div>
    </x-slot>
    <div class="p-6">
        <div class="max-w-2xl mx-auto">
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
                <div class="bg-gradient-to-r from-theme-from to-theme-to px-8 py-6">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center">
                            <x-icon name="edit" class="w-7 h-7 text-white" />
                        </div>
                        <div>
                            <h2 class="text-white text-xl font-bold">Edit Payment</h2>
                            <p class="text-sky-100 text-sm mt-0.5">Invoice {{ $payment->invoice->invoice_number ?? '—' }}</p>
                        </div>
                    </div>
                </div>
                <div class="px-8 py-7">
                    @php
                        $ic = fn($f) => 'w-full px-4 py-2.5 rounded-xl border text-gray-800 text-sm placeholder-gray-400 transition-all focus:outline-none focus:ring-2 focus:ring-theme-from/40 focus:border-theme-from ' . ($errors->has($f) ? 'border-red-400 bg-red-50' : 'border-gray-200 bg-gray-50 hover:border-theme-from');
                    @endphp
                    <form action="{{ route('payments.update', $payment) }}" method="POST" class="space-y-5">
                        @csrf @method('PUT')
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-1.5">Invoice</label>
                            <div class="px-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50 text-sm font-semibold text-gray-800">
                                {{ $payment->invoice->invoice_number ?? '—' }} — {{ $payment->invoice->patient->full_name ?? '—' }}
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-600 mb-1.5">Amount ($) <span class="text-red-400">*</span></label>
                                <input type="number" name="amount" step="0.01" min="0.01" value="{{ old('amount', $payment->amount) }}" class="{{ $ic('amount') }}">
                                <x-input-error :messages="$errors->get('amount')" class="mt-1" />
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-600 mb-1.5">Payment Date <span class="text-red-400">*</span></label>
                                <input type="date" name="paid_at" value="{{ old('paid_at', $payment->paid_at?->format('Y-m-d')) }}" class="{{ $ic('paid_at') }}">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-1.5">Payment Method <span class="text-red-400">*</span></label>
                            <div class="grid grid-cols-3 gap-2">
                                @foreach(['cash' => '💵 Cash', 'card' => '💳 Card', 'bank_transfer' => '🏦 Transfer'] as $val => $label)
                                    <label class="cursor-pointer">
                                        <input type="radio" name="payment_method" value="{{ $val }}" class="peer sr-only"
                                            {{ old('payment_method', $payment->payment_method) === $val ? 'checked' : '' }}>
                                        <div class="px-4 py-3 rounded-xl border-2 border-gray-200 text-center text-sm font-semibold text-gray-600 peer-checked:border-theme-from peer-checked:bg-theme-from/10 peer-checked:text-theme-from transition-all hover:border-theme-from/50">
                                            {{ $label }}
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-1.5">Notes</label>
                            <textarea name="notes" rows="2" class="{{ $ic('notes') }} resize-none">{{ old('notes', $payment->notes) }}</textarea>
                        </div>
                        <div class="border-t border-dashed border-gray-200 pt-2 flex items-center justify-end gap-3">
                            <a href="{{ route('payments.show', $payment) }}" class="px-5 py-2.5 rounded-xl border border-gray-200 text-sm font-semibold text-gray-600 hover:bg-gray-50 transition-all">Cancel</a>
                            <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-theme-from to-theme-to text-white text-sm font-bold shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all flex items-center gap-2">
                                <x-icon name="check-circle" class="w-4 h-4" /> Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
