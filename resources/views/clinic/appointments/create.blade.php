<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-widest font-semibold">Clinic › Appointments</p>
                <h1 class="text-xl font-bold text-gray-800">New Appointment</h1>
            </div>
        </div>
    </x-slot>

    <div class="p-6">
        <div class="max-w-2xl mx-auto">

            {{-- Card --}}
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">

                {{-- Card Header --}}
                <div class="bg-gradient-to-r from-theme-from to-theme-to px-8 py-6">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center shadow-inner">
                            <x-icon name="calendar" class="w-7 h-7 text-white" />
                        </div>
                        <div>
                            <h2 class="text-white text-xl font-bold">Schedule Appointment</h2>
                            <p class="text-sky-100 text-sm mt-0.5">Fill in the details below to book a new appointment</p>
                        </div>
                    </div>
                </div>

                {{-- Card Body --}}
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

                    <form action="{{ route('appointments.store') }}" method="POST" class="space-y-5">
                        @csrf
                        {{-- Idempotency Key --}}
                        <input type="hidden" name="idempotency_key" value="{{ old('idempotency_key', Str::uuid()) }}">

                        {{-- Patient Selection (Searchable) --}}
                        <x-searchable-select
                            name="patient_id"
                            :value="old('patient_id', request('patient_id'))"
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
                            :quickAddRoute="route('patients.create', ['return_to' => request()->fullUrl()])"
                            quickAddTitle="Add New Patient"
                        />

                        {{-- Doctor Selection --}}
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
                                <option value="" disabled {{ old('doctor_id', request('doctor_id')) ? '' : 'selected' }}>Select Doctor</option>
                                @foreach($doctors as $doctor)
                                    <option value="{{ $doctor->id }}" {{ old('doctor_id', request('doctor_id')) == $doctor->id ? 'selected' : '' }}>
                                        Dr. {{ $doctor->user->name ?? 'Unknown' }} ({{ $doctor->specialty }})
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('doctor_id')" class="mt-1.5" />
                        </div>

                        {{-- Date & Time side by side --}}
                        <div class="grid grid-cols-2 gap-4">
                            {{-- Appointment Date --}}
                            <div>
                                <label for="appointment_date" class="block text-sm font-semibold text-gray-600 mb-1.5">
                                    <span class="flex items-center gap-1.5">
                                        <x-icon name="calendar" class="w-4 h-4 text-theme-from" />
                                        Date <span class="text-red-400">*</span>
                                    </span>
                                </label>
                                <input type="date" id="appointment_date" name="appointment_date"
                                    value="{{ old('appointment_date', request('appointment_date', date('Y-m-d'))) }}"
                                    required
                                    class="{{ $inputClass('appointment_date') }}">
                                <x-input-error :messages="$errors->get('appointment_date')" class="mt-1.5" />
                            </div>

                            {{-- Appointment Time --}}
                            <div>
                                <label for="appointment_time" class="block text-sm font-semibold text-gray-600 mb-1.5">
                                    <span class="flex items-center gap-1.5">
                                        <x-icon name="clock" class="w-4 h-4 text-theme-from" />
                                        Time <span class="text-red-400">*</span>
                                    </span>
                                </label>
                                <input type="time" id="appointment_time" name="appointment_time"
                                    value="{{ old('appointment_time', request('appointment_time', now()->format('H:i'))) }}"
                                    required
                                    class="{{ $inputClass('appointment_time') }}">
                                <x-input-error :messages="$errors->get('appointment_time')" class="mt-1.5" />
                            </div>
                        </div>

                        {{-- Status Selection --}}
                        <div>
                            <label for="appointment_status" class="block text-sm font-semibold text-gray-600 mb-1.5">
                                <span class="flex items-center gap-1.5">
                                    <x-icon name="info" class="w-4 h-4 text-theme-from" />
                                    Initial Status <span class="text-red-400">*</span>
                                </span>
                            </label>
                            <select id="appointment_status" name="appointment_status" required
                                class="{{ $inputClass('appointment_status') }} appearance-none bg-no-repeat cursor-pointer"
                                style="{{ $selectStyle }}">
                                <option value="scheduled" {{ old('appointment_status', request('appointment_status')) === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                                <option value="completed" {{ old('appointment_status', request('appointment_status')) === 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ old('appointment_status', request('appointment_status')) === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                <option value="no_show"   {{ old('appointment_status', request('appointment_status')) === 'no_show'   ? 'selected' : '' }}>No Show</option>
                            </select>
                            <x-input-error :messages="$errors->get('appointment_status')" class="mt-1.5" />
                        </div>

                        {{-- Notes --}}
                        <div>
                            <label for="appointment_notes" class="block text-sm font-semibold text-gray-600 mb-1.5">
                                <span class="flex items-center gap-1.5">
                                    <x-icon name="clipboard" class="w-4 h-4 text-theme-from" />
                                    Notes (Optional)
                                </span>
                            </label>
                            <textarea id="appointment_notes" name="appointment_notes" rows="3"
                                placeholder="Additional details, reasons for visit…"
                                class="{{ $inputClass('appointment_notes') }} resize-none">{{ old('appointment_notes', request('appointment_notes')) }}</textarea>
                            <x-input-error :messages="$errors->get('appointment_notes')" class="mt-1.5" />
                        </div>

                        {{-- ── Follow-up / Return Visit ─────────────────────────────── --}}
                        <div x-data="{
                                appointments: @js($patientAppointments->map(fn($a) => [
                                    'id'     => $a->id,
                                    'label'  => $a->appointment_date?->format('M d, Y') . ' — Dr. ' . ($a->doctor->user->name ?? '?') . ' (' . ucfirst(str_replace('_', ' ', $a->appointment_status)) . ')',
                                    'date'   => $a->appointment_date?->format('M d, Y'),
                                    'doctor' => $a->doctor->user->name ?? '—',
                                    'status' => ucfirst(str_replace('_', ' ', $a->appointment_status)),
                                ])),
                                selected: '{{ old('parent_appointment_id', $parentAppointmentId ?? '') }}',
                                loading: false,
                                get last() { return this.appointments[0] ?? null; },
                                async fetchForPatient(pid) {
                                    if (!pid) { this.appointments = []; this.selected = ''; return; }
                                    this.loading = true;
                                    try {
                                        const r = await fetch(`/appointments/patient/${pid}/previous`);
                                        this.appointments = await r.json();
                                        if (!this.appointments.find(a => a.id === this.selected)) this.selected = '';
                                    } finally { this.loading = false; }
                                }
                            }"
                            x-init="
                                const self = $data;
                                window.addEventListener('searchable-select:change', e => {
                                    if (e.detail?.name === 'patient_id') self.fetchForPatient(e.detail.value);
                                });
                            "
                        >
                            <div class="border-t border-dashed border-gray-200 pt-4">
                                <label class="block text-sm font-semibold text-gray-600 mb-1.5">
                                    <span class="flex items-center gap-1.5">
                                        <x-icon name="arrow-up" class="w-4 h-4 text-theme-from" />
                                        Link to Previous Visit
                                        <span class="text-gray-400 font-normal text-xs">(Optional)</span>
                                    </span>
                                </label>

                                {{-- Last visit info box --}}
                                <template x-if="last">
                                    <div class="mb-2.5 flex items-center gap-3 px-3.5 py-2.5 rounded-xl bg-theme-from/5 border border-theme-from/20 text-xs text-theme-from">
                                        <x-icon name="calendar" class="w-4 h-4 text-theme-from/60 shrink-0" />
                                        <span>Last visit: <strong x-text="last.date"></strong> — Dr. <strong x-text="last.doctor"></strong>
                                            <span class="ml-1 px-1.5 py-0.5 rounded-full border text-[10px] font-bold"
                                                  :class="{
                                                    'bg-green-50 text-green-600 border-green-100': last.status === 'Completed',
                                                    'bg-blue-50 text-blue-600 border-blue-100': last.status === 'Scheduled',
                                                    'bg-red-50 text-red-600 border-red-100':     last.status === 'Cancelled',
                                                    'bg-amber-50 text-amber-600 border-amber-100': last.status === 'No show',
                                                  }"
                                                  x-text="last.status"></span>
                                        </span>
                                    </div>
                                </template>

                                <div class="relative">
                                    <select name="parent_appointment_id" x-model="selected"
                                        :disabled="loading || appointments.length === 0"
                                        class="{{ $inputClass('parent_appointment_id') }} appearance-none bg-no-repeat cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed"
                                        style="{{ $selectStyle }}">
                                        <option value="">— No previous visit linked —</option>
                                        <template x-for="apt in appointments" :key="apt.id">
                                            <option :value="apt.id" x-text="apt.label" :selected="apt.id === selected"></option>
                                        </template>
                                    </select>
                                    <template x-if="loading">
                                        <div class="absolute inset-y-0 right-8 flex items-center">
                                            <svg class="w-4 h-4 animate-spin text-theme-from" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                                            </svg>
                                        </div>
                                    </template>
                                    <p class="text-xs text-gray-400 mt-1" x-show="appointments.length === 0 && !loading">
                                        Select a patient first to see previous visits.
                                    </p>
                                </div>
                                <x-input-error :messages="$errors->get('parent_appointment_id')" class="mt-1.5" />
                            </div>
                        </div>

                        {{-- Divider --}}
                        <div class="border-t border-dashed border-gray-200 pt-2"></div>


                        {{-- Actions --}}
                        <div class="flex items-center justify-end gap-3">
                            <x-button-cancel/>
                            <x-button-submit>
                                <x-icon name="check-circle" class="w-4 h-4 mr-1.5" />
                                Schedule Appointment
                            </x-button-submit>
                        </div>

                    </form>
                </div>
            </div>

            {{-- Decorative hint --}}
            <p class="text-center text-xs text-gray-400 mt-4 flex items-center justify-center gap-1">
                <x-icon name="info" class="w-3.5 h-3.5 text-theme-from" />
                Fields marked with <span class="text-red-400 font-medium mx-0.5">*</span> are required
            </p>

        </div>
    </div>
</x-app-layout>
