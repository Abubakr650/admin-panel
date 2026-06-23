<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between w-full gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('orthodontics.cases.index') }}" class="p-2 rounded-xl hover:bg-gray-200 transition-colors text-gray-500">
                    <x-icon name="arrow-left" class="w-5 h-5" />
                </a>
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-widest font-semibold">Orthodontics › Cases</p>
                    <h1 class="text-lg font-bold text-gray-800 leading-tight">Edit Case</h1>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="p-6">
        <div class="max-w-3xl mx-auto">

            {{-- Card --}}
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">

                {{-- Card Header --}}
                <div class="bg-gradient-to-r from-theme-from to-theme-to px-8 py-6">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center shadow-inner">
                            <x-icon name="pencil" class="w-7 h-7 text-white" />
                        </div>
                        <div>
                            <h2 class="text-white text-xl font-bold">Update Orthodontic Case</h2>
                            <p class="text-sky-100 text-sm mt-0.5">Modify the details of the case</p>
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
                    @endphp

                    <form action="{{ route('orthodontics.cases.update', $case) }}" method="POST" class="space-y-5">
                        @csrf
                        @method('PUT')
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Patient --}}
                            <div>
                                <label for="patient_id" class="block text-sm font-semibold text-gray-600 mb-1.5">
                                    <span class="flex items-center gap-1.5">
                                        <x-icon name="user" class="w-4 h-4 text-theme-from" />
                                        Patient <span class="text-red-400">*</span>
                                    </span>
                                </label>
                                <select id="patient_id" name="patient_id" required
                                        class="{{ $inputClass('patient_id') }}">
                                    @foreach($patients as $patient)
                                        <option value="{{ $patient->id }}" {{ old('patient_id', $case->patient_id) == $patient->id ? 'selected' : '' }}>
                                            {{ $patient->full_name ?? $patient->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('patient_id')" class="mt-1.5" />
                            </div>

                            {{-- Doctor --}}
                            <div>
                                <label for="doctor_id" class="block text-sm font-semibold text-gray-600 mb-1.5">
                                    <span class="flex items-center gap-1.5">
                                        <x-icon name="user" class="w-4 h-4 text-theme-from" />
                                        Doctor <span class="text-red-400">*</span>
                                    </span>
                                </label>
                                <select id="doctor_id" name="doctor_id" required
                                        class="{{ $inputClass('doctor_id') }}">
                                    @foreach($doctors as $doctor)
                                        <option value="{{ $doctor->id }}" {{ old('doctor_id', $case->doctor_id) == $doctor->id ? 'selected' : '' }}>
                                            Dr. {{ $doctor->user->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('doctor_id')" class="mt-1.5" />
                            </div>

                            {{-- Total Amount --}}
                            <div>
                                <label for="total_amount" class="block text-sm font-semibold text-gray-600 mb-1.5">
                                    <span class="flex items-center gap-1.5">
                                        <x-icon name="currency-dollar" class="w-4 h-4 text-theme-from" />
                                        Total Amount <span class="text-red-400">*</span>
                                    </span>
                                </label>
                                <input type="number" step="0.01" id="total_amount" name="total_amount"
                                       value="{{ old('total_amount', $case->total_amount) }}" required placeholder="0.00"
                                       class="{{ $inputClass('total_amount') }}">
                                <x-input-error :messages="$errors->get('total_amount')" class="mt-1.5" />
                            </div>

                            {{-- Installment Amount --}}
                            <div>
                                <label for="installment_amount" class="block text-sm font-semibold text-gray-600 mb-1.5">
                                    <span class="flex items-center gap-1.5">
                                        <x-icon name="currency-dollar" class="w-4 h-4 text-theme-from" />
                                        Installment Amount <span class="text-red-400">*</span>
                                    </span>
                                </label>
                                <input type="number" step="0.01" id="installment_amount" name="installment_amount"
                                       value="{{ old('installment_amount', $case->installment_amount) }}" required placeholder="0.00"
                                       class="{{ $inputClass('installment_amount') }}">
                                <x-input-error :messages="$errors->get('installment_amount')" class="mt-1.5" />
                            </div>

                            {{-- Status --}}
                            <div class="md:col-span-2">
                                <label for="status" class="block text-sm font-semibold text-gray-600 mb-1.5">
                                    <span class="flex items-center gap-1.5">
                                        <x-icon name="badge-check" class="w-4 h-4 text-theme-from" />
                                        Status
                                    </span>
                                </label>
                                <select id="status" name="status"
                                        class="{{ $inputClass('status') }}">
                                    <option value="active" {{ old('status', $case->status) == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="completed" {{ old('status', $case->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="on_hold" {{ old('status', $case->status) == 'on_hold' ? 'selected' : '' }}>On Hold</option>
                                </select>
                                <x-input-error :messages="$errors->get('status')" class="mt-1.5" />
                            </div>
                        </div>

                        {{-- Diagnosis --}}
                        <div>
                            <label for="diagnosis" class="block text-sm font-semibold text-gray-600 mb-1.5">
                                <span class="flex items-center gap-1.5">
                                    <x-icon name="document-text" class="w-4 h-4 text-theme-from" />
                                    Diagnosis
                                </span>
                            </label>
                            <textarea id="diagnosis" name="diagnosis" rows="3"
                                      placeholder="Write diagnosis details..."
                                      class="{{ $inputClass('diagnosis') }} resize-none">{{ old('diagnosis', $case->diagnosis) }}</textarea>
                            <x-input-error :messages="$errors->get('diagnosis')" class="mt-1.5" />
                        </div>

                        {{-- Treatment Plan --}}
                        <div>
                            <label for="plan" class="block text-sm font-semibold text-gray-600 mb-1.5">
                                <span class="flex items-center gap-1.5">
                                    <x-icon name="clipboard-list" class="w-4 h-4 text-theme-from" />
                                    Treatment Plan
                                </span>
                            </label>
                            <textarea id="plan" name="plan" rows="5"
                                      placeholder="Write the detailed treatment plan..."
                                      class="{{ $inputClass('plan') }} resize-none">{{ old('plan', $case->plan) }}</textarea>
                            <x-input-error :messages="$errors->get('plan')" class="mt-1.5" />
                        </div>

                        {{-- Divider --}}
                        <div class="border-t border-dashed border-gray-200 pt-2"></div>

                        {{-- Actions --}}
                        <div class="flex items-center justify-end gap-3">
                            <x-button-cancel/>
                            <x-button-submit>
                                <x-icon name="pencil" class="w-4 h-4 mr-1.5" />
                                Update Case
                            </x-button-submit>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
