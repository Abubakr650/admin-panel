<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-widest font-semibold">Billing › Invoices</p>
                <h1 class="text-xl font-bold text-gray-800">Edit Invoice #{{ $invoice->invoice_number }}</h1>
            </div>
        </div>
    </x-slot>
    <div class="p-6">
        <div class="max-w-2xl mx-auto">
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
                <div class="bg-gradient-to-r from-theme-from to-theme-to px-8 py-6">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center">
                            <x-icon name="pencil" class="w-7 h-7 text-white" />
                        </div>
                        <div>
                            <h2 class="text-white text-xl font-bold">Edit Invoice</h2>
                            <p class="text-sky-100 text-sm mt-0.5">{{ $invoice->invoice_number }} — {{ $invoice->patient->full_name ?? '—' }}</p>
                        </div>
                    </div>
                </div>
                <div class="px-8 py-7">
                    @php
                        $ic = fn($f) => 'w-full px-4 py-2.5 rounded-xl border text-gray-800 text-sm placeholder-gray-400 transition-all focus:outline-none focus:ring-2 focus:ring-theme-from/40 focus:border-theme-from ' . ($errors->has($f) ? 'border-red-400 bg-red-50' : 'border-gray-200 bg-gray-50 hover:border-theme-from');
                        $ss = "background-image: url('data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' fill=\'none\' viewBox=\'0 0 24 24\' stroke=\'%236b7280\'%3E%3Cpath stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M19 9l-7 7-7-7\'/%3E%3C/svg%3E'); background-position: right 12px center; background-size: 16px;";
                    @endphp
                    <form action="{{ route('invoices.update', $invoice) }}" method="POST" class="space-y-5"
                          x-data="{ discount: {{ old('discount_percent', $invoice->discount_percent) }}, total: {{ $invoice->total_amount }}, get final() { return Math.max(0, this.total * (1 - this.discount/100)); } }">
                        @csrf @method('PUT')

                        {{-- Read-only info --}}
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-600 mb-1.5">Patient</label>
                                <div class="px-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50 text-sm font-semibold text-gray-800">{{ $invoice->patient->full_name ?? '—' }}</div>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-600 mb-1.5">Subtotal</label>
                                <div class="px-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50 text-sm font-bold text-gray-800">${{ number_format($invoice->total_amount, 2) }}</div>
                            </div>
                        </div>

                        {{-- Editable fields --}}
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-600 mb-1.5">Discount % <span class="text-red-400">*</span></label>
                                <input type="number" name="discount_percent" min="0" max="100"
                                    x-model.number="discount"
                                    value="{{ old('discount_percent', $invoice->discount_percent) }}"
                                    class="{{ $ic('discount_percent') }}">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-600 mb-1.5">Final Amount</label>
                                <div class="px-4 py-2.5 rounded-xl border border-theme-from/30 bg-theme-from/5 text-lg font-extrabold text-theme-from" x-text="'$' + final.toFixed(2)"></div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-600 mb-1.5">Payment Status <span class="text-red-400">*</span></label>
                            <select name="payment_status" required class="{{ $ic('payment_status') }} appearance-none bg-no-repeat cursor-pointer" style="{{ $ss }}">
                                @foreach(['unpaid' => 'Unpaid', 'partial' => 'Partial', 'paid' => 'Paid'] as $val => $label)
                                    <option value="{{ $val }}" {{ old('payment_status', $invoice->payment_status) === $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            <p class="text-xs text-amber-600 mt-1">⚠️ Status auto-syncs when payments are recorded. Manual override use only.</p>
                        </div>

                        <div class="border-t border-dashed border-gray-200 pt-2 flex items-center justify-end gap-3">
                            <a href="{{ route('invoices.show', $invoice) }}" class="px-5 py-2.5 rounded-xl border border-gray-200 text-sm font-semibold text-gray-600 hover:bg-gray-50 transition-all">Cancel</a>
                            <x-button-submit><x-icon name="check-circle" class="w-4 h-4 mr-1.5" /> Save Changes</x-button-submit>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
