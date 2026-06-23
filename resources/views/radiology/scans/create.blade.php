<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between w-full gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('radiology.scans.index') }}" class="p-2 rounded-xl hover:bg-gray-200 transition-colors text-gray-500">
                    <x-icon name="arrow-left" class="w-5 h-5" />
                </a>
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-widest font-semibold">Radiology › Scans</p>
                    <h1 class="text-lg font-bold text-gray-800 leading-tight">New Radiology Scan & Analysis</h1>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="p-6">
        <div class="max-w-3xl mx-auto">

            {{-- Card --}}
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100" x-data="{ loading: false }">

                {{-- Card Header --}}
                <div class="bg-gradient-to-r from-theme-from to-theme-to px-8 py-6">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center shadow-inner">
                            <x-icon name="photograph" class="w-7 h-7 text-white" />
                        </div>
                        <div>
                            <h2 class="text-white text-xl font-bold">Upload Dental X-Ray & Details</h2>
                            <p class="text-sky-100 text-sm mt-0.5">Fill in the details below and get AI-powered analysis</p>
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

                    <form action="{{ route('radiology.scans.store') }}" method="POST" enctype="multipart/form-data" @submit="loading = true" class="space-y-5">
                        @csrf
                        
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
                                    <option value="">— Select Patient —</option>
                                    @foreach($patients as $patient)
                                        <option value="{{ $patient->id }}" {{ old('patient_id') == $patient->id ? 'selected' : '' }}>
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
                                        <x-icon name="user-circle" class="w-4 h-4 text-theme-from" />
                                        Doctor <span class="text-red-400">*</span>
                                    </span>
                                </label>
                                <select id="doctor_id" name="doctor_id" required
                                        class="{{ $inputClass('doctor_id') }}">
                                    <option value="">— Select Doctor —</option>
                                    @foreach($doctors as $doctor)
                                        <option value="{{ $doctor->id }}" {{ old('doctor_id') == $doctor->id ? 'selected' : '' }}>
                                            Dr. {{ $doctor->user->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('doctor_id')" class="mt-1.5" />
                            </div>

                            {{-- Radiology Type --}}
                            <div>
                                <label for="radiology_type" class="block text-sm font-semibold text-gray-600 mb-1.5">
                                    <span class="flex items-center gap-1.5">
                                        <x-icon name="document-search" class="w-4 h-4 text-theme-from" />
                                        Radiology Type <span class="text-red-400">*</span>
                                    </span>
                                </label>
                                <input type="text" id="radiology_type" name="radiology_type"
                                       value="{{ old('radiology_type') }}" required placeholder="e.g. Panoramic X-Ray"
                                       class="{{ $inputClass('radiology_type') }}">
                                <x-input-error :messages="$errors->get('radiology_type')" class="mt-1.5" />
                            </div>

                            {{-- Service (Billing) --}}
                            <div>
                                <label for="service_id" class="block text-sm font-semibold text-gray-600 mb-1.5">
                                    <span class="flex items-center gap-1.5">
                                        <x-icon name="currency-dollar" class="w-4 h-4 text-theme-from" />
                                        Service (Billing) <span class="text-red-400">*</span>
                                    </span>
                                </label>
                                <select id="service_id" name="service_id" required
                                        class="{{ $inputClass('service_id') }}">
                                    <option value="">— Select Service —</option>
                                    @foreach($services as $service)
                                        <option value="{{ $service->id }}" {{ old('service_id') == $service->id ? 'selected' : '' }}>
                                            {{ $service->name }} ({{ number_format($service->price, 2) }})
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('service_id')" class="mt-1.5" />
                            </div>
                        </div>

                        {{-- Diagnosis --}}
                        <div>
                            <label for="diagnosis" class="block text-sm font-semibold text-gray-600 mb-1.5">
                                <span class="flex items-center gap-1.5">
                                    <x-icon name="clipboard-list" class="w-4 h-4 text-theme-from" />
                                    Diagnosis / Notes
                                </span>
                            </label>
                            <textarea id="diagnosis" name="diagnosis" rows="3"
                                      placeholder="Describe the findings or clinical notes..."
                                      class="{{ $inputClass('diagnosis') }} resize-none">{{ old('diagnosis') }}</textarea>
                            <x-input-error :messages="$errors->get('diagnosis')" class="mt-1.5" />
                        </div>

                        {{-- X-Ray Image --}}
                        <div>
                            <label for="image" class="block text-sm font-semibold text-gray-600 mb-1.5">
                                <span class="flex items-center gap-1.5">
                                    <x-icon name="upload" class="w-4 h-4 text-theme-from" />
                                    X-Ray Image <span class="text-red-400">*</span>
                                </span>
                            </label>
                            
                            <div class="flex items-center justify-center w-full">
                                <label for="image" class="flex flex-col items-center justify-center w-full h-48 border-2 border-gray-300 border-dashed rounded-xl cursor-pointer bg-gray-50 hover:bg-gray-100 transition-colors {{ $errors->has('image') ? 'border-red-400 bg-red-50' : '' }}">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <x-icon name="cloud-upload" class="w-10 h-10 text-gray-400 mb-3" />
                                        <p class="mb-2 text-sm text-gray-500"><span class="font-semibold">Click to upload</span> or drag and drop</p>
                                        <p class="text-xs text-gray-500">PNG, JPG or JPEG (MAX. 10MB)</p>
                                    </div>
                                    <input id="image" name="image" type="file" class="hidden" accept="image/jpeg,image/png,image/jpg" required />
                                </label>
                            </div>
                            <x-input-error :messages="$errors->get('image')" class="mt-1.5" />
                            
                            <!-- Custom Error for catch block -->
                            @if ($errors->has('error'))
                                <div class="mt-3 p-4 rounded-xl bg-red-50 border border-red-200 flex items-start gap-3">
                                    <x-icon name="exclamation-circle" class="w-5 h-5 text-red-500 shrink-0 mt-0.5" />
                                    <div class="text-sm text-red-600">
                                        {{ $errors->first('error') }}
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{-- Divider --}}
                        <div class="border-t border-dashed border-gray-200 pt-2"></div>

                        {{-- Actions --}}
                        <div class="flex items-center justify-end gap-3">
                            <x-button-cancel href="{{ route('radiology.scans.index') }}" />
                            <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-gradient-to-r from-theme-from to-theme-to text-white text-sm font-semibold rounded-xl hover:shadow-lg hover:shadow-theme-from/20 transition-all duration-200 active:scale-95" :disabled="loading">
                                <span x-show="!loading" class="flex items-center gap-2">
                                    <x-icon name="sparkles" class="w-4 h-4" />
                                    Create Scan & Analyze
                                </span>
                                <span x-show="loading" class="flex items-center gap-2" style="display: none;">
                                    <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Processing...
                                </span>
                            </button>
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

    {{-- Script to show filename --}}
    <script>
        document.getElementById('image').addEventListener('change', function(e) {
            var fileName = e.target.files[0]?.name;
            if (fileName) {
                var labelText = this.previousElementSibling.querySelector('p:first-of-type');
                labelText.innerHTML = '<span class="font-semibold text-theme-from">' + fileName + '</span>';
            }
        });
    </script>
</x-app-layout>
