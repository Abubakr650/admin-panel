<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-widest font-semibold">Clinic › Treatments</p>
                <h1 class="text-xl font-bold text-gray-800">New Treatment</h1>
            </div>
        </div>
    </x-slot>

    <div class="p-6">
        <div class="max-w-2xl mx-auto">
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">

                {{-- Card Header --}}
                <div class="bg-gradient-to-r from-theme-from to-theme-to px-8 py-6">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center shadow-inner">
                            <x-icon name="clipboard" class="w-7 h-7 text-white" />
                        </div>
                        <div>
                            <h2 class="text-white text-xl font-bold">Record Treatment</h2>
                            <p class="text-sky-100 text-sm mt-0.5">Record a clinical procedure or pharmacy dispense</p>
                        </div>
                    </div>
                </div>

                {{-- Card Body --}}
                <div class="px-8 py-7" x-data="treatmentForm()">
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

                    <form action="{{ route('treatments.store') }}" method="POST" class="space-y-5">
                        @csrf

                        @if($selectedAppointmentId)
                            <input type="hidden" name="appointment_id" value="{{ $selectedAppointmentId }}">
                        @endif

                        {{-- Patient --}}
                        <x-searchable-select
                            name="patient_id"
                            :value="old('patient_id', $selectedPatientId)"
                            :options="$patients->map(fn($p) => [
                                'id' => $p->id,
                                'name' => $p->full_name,
                                'subtext' => (string)($p->phone ?? '')
                            ])"
                            label="Patient"
                            icon="user"
                            placeholder="Select Patient"
                            searchPlaceholder="Type to search patients..."
                            required
                        />

                        {{-- Doctor --}}
                        <div>
                            <label for="doctor_id" class="block text-sm font-semibold text-gray-600 mb-1.5">
                                <span class="flex items-center gap-1.5">
                                    <x-icon name="users" class="w-4 h-4 text-theme-from" />
                                    Doctor <span class="text-red-400">*</span>
                                </span>
                            </label>
                            <select id="doctor_id" name="doctor_id" required
                                class="{{ $inputClass('doctor_id') }} appearance-none bg-no-repeat cursor-pointer"
                                style="{{ $selectStyle }}">
                                <option value="" disabled {{ old('doctor_id') ? '' : 'selected' }}>Select Doctor</option>
                                @foreach($doctors as $doctor)
                                    <option value="{{ $doctor->id }}" {{ old('doctor_id') == $doctor->id ? 'selected' : '' }}>
                                        Dr. {{ $doctor->user->name ?? 'Unknown' }} ({{ $doctor->specialty }})
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('doctor_id')" class="mt-1.5" />
                        </div>

                        {{-- Treatment Type --}}
                        <div>
                            <label for="type" class="block text-sm font-semibold text-gray-600 mb-1.5">
                                <span class="flex items-center gap-1.5">
                                    <x-icon name="info" class="w-4 h-4 text-theme-from" />
                                    Treatment Type <span class="text-red-400">*</span>
                                </span>
                            </label>
                            <select id="type" name="type" required x-model="type" @change="onTypeChange()"
                                class="{{ $inputClass('type') }} appearance-none bg-no-repeat cursor-pointer"
                                style="{{ $selectStyle }}">
                                <option value="consultation">Consultation</option>
                                <option value="filling">Filling</option>
                                <option value="extraction">Extraction</option>
                                <option value="cleaning">Cleaning</option>
                                <option value="cosmetic">Cosmetic</option>
                                <option value="radiology">Radiology</option>
                                <option value="orthodontic_session">Orthodontic Session</option>
                                <option value="pharmacy_dispense">Pharmacy Dispense</option>
                                <option value="other">Other</option>
                            </select>
                            <x-input-error :messages="$errors->get('type')" class="mt-1.5" />
                        </div>

                        {{-- Service (shown for non-pharmacy types) --}}
                        <div x-show="type !== 'pharmacy_dispense'" x-transition>
                            <label for="service_id" class="block text-sm font-semibold text-gray-600 mb-1.5">
                                <span class="flex items-center gap-1.5">
                                    <x-icon name="lightning" class="w-4 h-4 text-theme-from" />
                                    Service (Price Reference)
                                </span>
                            </label>
                            <select id="service_id" name="service_id" @change="onServiceChange($event)"
                                class="{{ $inputClass('service_id') }} appearance-none bg-no-repeat cursor-pointer"
                                style="{{ $selectStyle }}">
                                <option value="">— No service selected —</option>
                                @foreach($services as $service)
                                    <option value="{{ $service->id }}" data-price="{{ $service->default_price }}">
                                        {{ $service->name }} — ${{ number_format($service->default_price, 2) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Pharmacy Batch (shown for pharmacy_dispense only) --}}
                        <div x-show="type === 'pharmacy_dispense'" x-transition>
                            <label for="pharmacy_batch_id" class="block text-sm font-semibold text-gray-600 mb-1.5">
                                <span class="flex items-center gap-1.5">
                                    <x-icon name="clipboard" class="w-4 h-4 text-theme-from" />
                                    Pharmacy Item <span class="text-red-400">*</span>
                                </span>
                            </label>
                            <select id="pharmacy_batch_id" name="pharmacy_batch_id" @change="onBatchChange($event)"
                                class="{{ $inputClass('pharmacy_batch_id') }} appearance-none bg-no-repeat cursor-pointer"
                                style="{{ $selectStyle }}">
                                <option value="">— Select Medicine —</option>
                                @foreach($batches as $batch)
                                    <option value="{{ $batch->id }}"
                                            data-price="{{ $batch->pharmacyItem->default_price }}"
                                            data-remaining="{{ $batch->remaining_quantity }}">
                                        {{ $batch->pharmacyItem->commercial_name }} — Batch: {{ $batch->batch_number }}
                                        (Qty: {{ $batch->remaining_quantity }}) — ${{ number_format($batch->pharmacyItem->default_price, 2) }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-400 mt-1" x-show="maxQty > 0">
                                Available: <strong x-text="maxQty"></strong> units
                            </p>
                        </div>

                        {{-- Quantity, Price, Discount --}}
                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <label for="quantity" class="block text-sm font-semibold text-gray-600 mb-1.5">
                                    Quantity <span class="text-red-400">*</span>
                                </label>
                                <input type="number" id="quantity" name="quantity" min="1"
                                    x-model.number="quantity" @input="calcTotal()"
                                    class="{{ $inputClass('quantity') }}" value="1">
                                <x-input-error :messages="$errors->get('quantity')" class="mt-1.5" />
                            </div>
                            <div>
                                <label for="price" class="block text-sm font-semibold text-gray-600 mb-1.5">
                                    Unit Price ($) <span class="text-red-400">*</span>
                                </label>
                                <input type="number" id="price" name="price" step="0.01" min="0"
                                    x-model.number="price" @input="calcTotal()"
                                    class="{{ $inputClass('price') }}">
                                <x-input-error :messages="$errors->get('price')" class="mt-1.5" />
                            </div>
                            <div>
                                <label for="discount" class="block text-sm font-semibold text-gray-600 mb-1.5">
                                    Discount ($)
                                </label>
                                <input type="number" id="discount" name="discount" step="0.01" min="0"
                                    x-model.number="discount" @input="calcTotal()"
                                    class="{{ $inputClass('discount') }}" value="0">
                            </div>
                        </div>

                        {{-- Total Display --}}
                        <div class="rounded-xl bg-gradient-to-r from-theme-from/5 to-theme-to/5 border border-theme-from/20 px-5 py-3 flex items-center justify-between">
                            <span class="text-sm font-semibold text-gray-600">Total Amount</span>
                            <span class="text-2xl font-extrabold text-theme-from" x-text="'$' + total.toFixed(2)"></span>
                        </div>

                        {{-- Status --}}
                        <div>
                            <label for="status" class="block text-sm font-semibold text-gray-600 mb-1.5">
                                <span class="flex items-center gap-1.5">
                                    <x-icon name="check-circle" class="w-4 h-4 text-theme-from" />
                                    Status <span class="text-red-400">*</span>
                                </span>
                            </label>
                            <select id="status" name="status" required
                                class="{{ $inputClass('status') }} appearance-none bg-no-repeat cursor-pointer"
                                style="{{ $selectStyle }}">
                                <option value="completed" selected>Completed</option>
                                <option value="in_progress">In Progress</option>
                                <option value="planned">Planned</option>
                            </select>
                        </div>

                        {{-- Description --}}
                        <div>
                            <label for="description" class="block text-sm font-semibold text-gray-600 mb-1.5">
                                <span class="flex items-center gap-1.5">
                                    <x-icon name="clipboard" class="w-4 h-4 text-theme-from" />
                                    Description (Optional)
                                </span>
                            </label>
                            <textarea id="description" name="description" rows="3"
                                placeholder="Treatment notes…"
                                class="{{ $inputClass('description') }} resize-none">{{ old('description') }}</textarea>
                        </div>

                        {{-- Divider --}}
                        <div class="border-t border-dashed border-gray-200 pt-2"></div>

                        {{-- Actions --}}
                        <div class="flex items-center justify-end gap-3">
                            <x-button-cancel/>
                            <x-button-submit>
                                <x-icon name="check-circle" class="w-4 h-4 mr-1.5" />
                                Save Treatment
                            </x-button-submit>
                        </div>
                    </form>
                </div>
            </div>

            <p class="text-center text-xs text-gray-400 mt-4 flex items-center justify-center gap-1">
                <x-icon name="info" class="w-3.5 h-3.5 text-theme-from" />
                Fields marked with <span class="text-red-400 font-medium mx-0.5">*</span> are required
            </p>
        </div>
    </div>

    <script>
        function treatmentForm() {
            return {
                type: '{{ old("type", "consultation") }}',
                price: {{ old('price', 0) }},
                quantity: {{ old('quantity', 1) }},
                discount: {{ old('discount', 0) }},
                total: 0,
                maxQty: 0,

                init() { this.calcTotal(); },

                calcTotal() {
                    this.total = Math.max(0, (this.price * this.quantity) - this.discount);
                },

                onTypeChange() {
                    if (this.type !== 'pharmacy_dispense') {
                        document.getElementById('pharmacy_batch_id').value = '';
                        this.maxQty = 0;
                    } else {
                        document.getElementById('service_id').value = '';
                    }
                    this.price = 0;
                    this.calcTotal();
                },

                onServiceChange(e) {
                    const opt = e.target.selectedOptions[0];
                    this.price = parseFloat(opt?.dataset?.price || 0);
                    this.calcTotal();
                },

                onBatchChange(e) {
                    const opt = e.target.selectedOptions[0];
                    this.price = parseFloat(opt?.dataset?.price || 0);
                    this.maxQty = parseInt(opt?.dataset?.remaining || 0);
                    this.quantity = 1;
                    this.calcTotal();
                }
            };
        }
    </script>
</x-app-layout>
