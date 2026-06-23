<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-widest font-semibold">Billing › Invoices</p>
                <h1 class="text-xl font-bold text-gray-800">
                    {{ $appointment ? 'Create Invoice for Appointment' : 'New Invoice' }}
                </h1>
            </div>
        </div>
    </x-slot>

    <div class="p-6">
        <div class="max-w-3xl mx-auto">
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">

                {{-- Card Header --}}
                <div class="bg-gradient-to-r from-theme-from to-theme-to px-8 py-6">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center shadow-inner">
                            <x-icon name="document" class="w-7 h-7 text-white" />
                        </div>
                        <div>
                            <h2 class="text-white text-xl font-bold">Create Invoice</h2>
                            @if($appointment)
                                <p class="text-sky-100 text-sm mt-0.5">
                                    {{ $appointment->patient->full_name }} — {{ $appointment->appointment_date?->format('M d, Y') }}
                                </p>
                            @else
                                <p class="text-sky-100 text-sm mt-0.5">Select treatments to include in this invoice</p>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Form --}}
                <div class="px-8 py-7" x-data="invoiceForm()">
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

                    <form action="{{ route('invoices.store') }}" method="POST" class="space-y-6">
                        @csrf

                        {{-- Patient & Doctor (read-only if from appointment) --}}
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-600 mb-1.5">
                                    <span class="flex items-center gap-1.5"><x-icon name="user" class="w-4 h-4 text-theme-from" /> Patient <span class="text-red-400">*</span></span>
                                </label>
                                @if($appointment)
                                    <div class="px-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50 text-sm font-semibold text-gray-800">
                                        {{ $appointment->patient->full_name }}
                                    </div>
                                    <input type="hidden" name="patient_id" value="{{ $selectedPatientId }}">
                                @else
                                    <select name="patient_id" required class="{{ $inputClass('patient_id') }} appearance-none bg-no-repeat cursor-pointer" style="{{ $selectStyle }}">
                                        <option value="">— Select Patient —</option>
                                        @foreach($patients as $patient)
                                            <option value="{{ $patient->id }}" {{ old('patient_id', $selectedPatientId) == $patient->id ? 'selected' : '' }}>
                                                {{ $patient->full_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-600 mb-1.5">
                                    <span class="flex items-center gap-1.5"><x-icon name="users" class="w-4 h-4 text-theme-from" /> Doctor <span class="text-red-400">*</span></span>
                                </label>
                                @if($appointment)
                                    <div class="px-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50 text-sm font-semibold text-gray-800">
                                        Dr. {{ $appointment->doctor->user->name }}
                                    </div>
                                    <input type="hidden" name="doctor_id" value="{{ $selectedDoctorId }}">
                                @else
                                    <select name="doctor_id" required class="{{ $inputClass('doctor_id') }} appearance-none bg-no-repeat cursor-pointer" style="{{ $selectStyle }}">
                                        <option value="">— Select Doctor —</option>
                                        @foreach($doctors as $doctor)
                                            <option value="{{ $doctor->id }}" {{ old('doctor_id', $selectedDoctorId) == $doctor->id ? 'selected' : '' }}>
                                                Dr. {{ $doctor->user->name ?? 'Unknown' }}
                                            </option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>
                        </div>

                        {{-- Currency & Discount --}}
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-600 mb-1.5">
                                    <span class="flex items-center gap-1.5"><x-icon name="currency" class="w-4 h-4 text-theme-from" /> Currency <span class="text-red-400">*</span></span>
                                </label>
                                <select name="currency_id" required class="{{ $inputClass('currency_id') }} appearance-none bg-no-repeat cursor-pointer" style="{{ $selectStyle }}">
                                    @foreach($currencies as $currency)
                                        <option value="{{ $currency->id }}" {{ old('currency_id') == $currency->id ? 'selected' : '' }}>
                                            {{ $currency->name }} ({{ $currency->code }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-600 mb-1.5">Discount %</label>
                                <input type="number" name="discount_percent" min="0" max="100"
                                    x-model.number="discountPercent" @input="calcFinal()"
                                    value="{{ old('discount_percent', 0) }}"
                                    class="{{ $inputClass('discount_percent') }}">
                            </div>
                        </div>

                        {{-- Pending Treatments Selection --}}
                        <div class="border-t border-dashed border-gray-200 pt-4">
                            <label class="block text-sm font-semibold text-gray-600 mb-3">
                                <span class="flex items-center gap-1.5">
                                    <x-icon name="clipboard" class="w-4 h-4 text-theme-from" />
                                    Select Treatments to Invoice <span class="text-red-400">*</span>
                                </span>
                            </label>

                            @if($pendingTreatments->isEmpty())
                                <div class="p-5 rounded-xl bg-amber-50 border border-amber-100 text-sm text-amber-700 flex items-center gap-3">
                                    <x-icon name="info" class="w-5 h-5 text-amber-400 shrink-0" />
                                    No pending treatments found. Add treatments to the appointment first.
                                </div>
                            @else
                                <div class="rounded-xl border border-gray-100 overflow-hidden">
                                    <table class="min-w-full">
                                        <thead>
                                            <tr class="bg-gray-50/70">
                                                <th class="w-10 px-4 py-2.5"></th>
                                                <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Type</th>
                                                <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Description</th>
                                                <th class="px-4 py-2.5 text-center text-xs font-semibold text-gray-500 uppercase">Qty</th>
                                                <th class="px-4 py-2.5 text-right text-xs font-semibold text-gray-500 uppercase">Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-100">
                                            @foreach($pendingTreatments as $treatment)
                                                <tr class="hover:bg-gray-50 cursor-pointer" @click="toggleTreatment('{{ $treatment->id }}', {{ $treatment->total }})">
                                                    <td class="px-4 py-3 text-center">
                                                        <input type="checkbox" name="treatment_ids[]"
                                                            value="{{ $treatment->id }}"
                                                            x-model="selectedIds"
                                                            @change="calcFinal()"
                                                            class="w-4 h-4 rounded text-theme-from focus:ring-theme-from">
                                                    </td>
                                                    <td class="px-4 py-3">
                                                        @php
                                                            $tc = [
                                                                'consultation' => 'bg-indigo-50 text-indigo-600 border-indigo-100',
                                                                'filling' => 'bg-blue-50 text-blue-600 border-blue-100',
                                                                'extraction' => 'bg-red-50 text-red-600 border-red-100',
                                                                'cleaning' => 'bg-teal-50 text-teal-600 border-teal-100',
                                                                'pharmacy_dispense' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                                                            ];
                                                        @endphp
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $tc[$treatment->type] ?? 'bg-gray-50 text-gray-600 border-gray-100' }}">
                                                            {{ ucfirst(str_replace('_', ' ', $treatment->type)) }}
                                                        </span>
                                                    </td>
                                                    <td class="px-4 py-3 text-sm text-gray-700">
                                                        {{ $treatment->service->name ?? $treatment->description ?? '—' }}
                                                    </td>
                                                    <td class="px-4 py-3 text-center text-sm text-gray-600">{{ $treatment->quantity }}</td>
                                                    <td class="px-4 py-3 text-right font-bold text-gray-800">${{ number_format($treatment->total, 2) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                {{-- Select all --}}
                                <div class="mt-2 flex items-center gap-2">
                                    <input type="checkbox" id="select_all" @change="toggleAll($event)"
                                        class="w-4 h-4 rounded text-theme-from focus:ring-theme-from">
                                    <label for="select_all" class="text-xs text-gray-500 cursor-pointer">Select all treatments</label>
                                </div>
                            @endif
                        </div>

                        {{-- Invoice Total Summary --}}
                        <div class="rounded-xl bg-gradient-to-r from-theme-from/5 to-theme-to/5 border border-theme-from/20 px-6 py-4 space-y-2">
                            <div class="flex justify-between text-sm text-gray-600">
                                <span>Subtotal</span>
                                <span class="font-semibold" x-text="'$' + subtotal.toFixed(2)"></span>
                            </div>
                            <div class="flex justify-between text-sm text-gray-600">
                                <span>Discount (<span x-text="discountPercent"></span>%)</span>
                                <span class="font-semibold text-red-500" x-text="'-$' + discountAmount.toFixed(2)"></span>
                            </div>
                            <div class="border-t border-theme-from/20 pt-2 flex justify-between">
                                <span class="font-bold text-gray-800">Final Total</span>
                                <span class="text-2xl font-extrabold text-theme-from" x-text="'$' + finalAmount.toFixed(2)"></span>
                            </div>
                        </div>

                        <x-input-error :messages="$errors->get('treatment_ids')" class="mt-1" />

                        <div class="border-t border-dashed border-gray-200 pt-2"></div>
                        <div class="flex items-center justify-end gap-3">
                            <x-button-cancel/>
                            <x-button-submit>
                                <x-icon name="document" class="w-4 h-4 mr-1.5" />
                                Generate Invoice
                            </x-button-submit>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function invoiceForm() {
            const treatments = @json($pendingTreatments->mapWithKeys(fn($t) => [$t->id => (float)$t->total]));
            return {
                selectedIds: [],
                discountPercent: 0,
                get subtotal() {
                    return this.selectedIds.reduce((s, id) => s + (treatments[id] || 0), 0);
                },
                get discountAmount() { return this.subtotal * this.discountPercent / 100; },
                get finalAmount()    { return Math.max(0, this.subtotal - this.discountAmount); },
                calcFinal() {}, // reactivity handled by getters
                toggleTreatment(id, amount) {
                    const idx = this.selectedIds.indexOf(id);
                    idx === -1 ? this.selectedIds.push(id) : this.selectedIds.splice(idx, 1);
                },
                toggleAll(e) {
                    this.selectedIds = e.target.checked
                        ? Object.keys(treatments)
                        : [];
                }
            };
        }
    </script>
</x-app-layout>
