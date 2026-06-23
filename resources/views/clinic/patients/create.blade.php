<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wider font-medium">Clinic › Patients</p>
                <h1 class="text-xl font-bold text-gray-800">New Patient</h1>
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
                            <x-icon name="user-add" class="w-7 h-7 text-white" />
                        </div>
                        <div>
                            <h2 class="text-white text-xl font-bold">Patient Registration</h2>
                            <p class="text-sky-100 text-sm mt-0.5">Fill in the details below to register a new patient</p>
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

                    <form action="{{ route('patients.store') }}" method="POST" class="space-y-5">
                        @csrf
                        {{-- Idempotency Key --}}
                        <input type="hidden" name="idempotency_key" value="{{ Str::uuid() }}">
                        
                        {{-- Return To --}}
                        @if(isset($returnTo))
                            <input type="hidden" name="return_to" value="{{ $returnTo }}">
                        @endif

                        {{-- Full Name --}}
                        <div class="group">
                            <label for="full_name" class="block text-sm font-semibold text-gray-600 mb-1.5">
                                <span class="flex items-center gap-1.5">
                                    <x-icon name="user" class="w-4 h-4 text-theme-from" />
                                    Full Name <span class="text-red-400">*</span>
                                </span>
                            </label>
                            <input type="text" id="full_name" name="full_name"
                                value="{{ old('full_name') }}"
                                required autofocus
                                placeholder="Full name"
                                class="{{ $inputClass('full_name') }}">
                            <x-input-error :messages="$errors->get('full_name')" class="mt-1.5" />
                        </div>

                        {{-- Gender & Age --}}
                        <div class="grid grid-cols-2 gap-4">

                            {{-- Gender --}}
                            <div>
                                <label for="gender" class="block text-sm font-semibold text-gray-600 mb-1.5">
                                    <span class="flex items-center gap-1.5">
                                        <x-icon name="gender" class="w-4 h-4 text-theme-from" />
                                        Gender <span class="text-red-400">*</span>
                                    </span>
                                </label>
                                <select id="gender" name="gender" required
                                    class="{{ $inputClass('gender') }} appearance-none bg-no-repeat cursor-pointer"
                                    style="background-image: url('data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' fill=\'none\' viewBox=\'0 0 24 24\' stroke=\'%236b7280\'%3E%3Cpath stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M19 9l-7 7-7-7\'/%3E%3C/svg%3E'); background-position: right 12px center; background-size: 16px;">
                                    <option value="male"   {{ old('gender') === 'male'   ? 'selected' : '' }}>♂ Male</option>
                                    <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>♀ Female</option>
                                </select>
                                <x-input-error :messages="$errors->get('gender')" class="mt-1.5" />
                            </div>

                            {{-- Age --}}
                            <div>
                                <label for="age" class="block text-sm font-semibold text-gray-600 mb-1.5">
                                    <span class="flex items-center gap-1.5">
                                        <x-icon name="calendar" class="w-4 h-4 text-theme-from" />
                                        Age <span class="text-red-400">*</span>
                                    </span>
                                </label>
                                <input type="number" id="age" name="age"
                                    value="{{ old('age') }}"
                                    min="1" max="120"
                                    required
                                    placeholder="e.g. 30"
                                    class="{{ $inputClass('age') }}">
                                <x-input-error :messages="$errors->get('age')" class="mt-1.5" />
                            </div>
                        </div>

                        {{-- Phone --}}
                        <div>
                            <label for="phone" class="block text-sm font-semibold text-gray-600 mb-1.5">
                                <span class="flex items-center gap-1.5">
                                    <x-icon name="phone" class="w-4 h-4 text-theme-from" />
                                    Phone Number
                                </span>
                            </label>
                            <input type="text" id="phone" name="phone"
                                value="{{ old('phone') }}"
                                placeholder="+967 7xx xxx xxx"
                                class="{{ $inputClass('phone') }}">
                            <x-input-error :messages="$errors->get('phone')" class="mt-1.5" />
                        </div>

                        {{-- Address --}}
                        <div>
                            <label for="address" class="block text-sm font-semibold text-gray-600 mb-1.5">
                                <span class="flex items-center gap-1.5">
                                    <x-icon name="location" class="w-4 h-4 text-theme-from" />
                                    Address
                                </span>
                            </label>
                            <textarea id="address" name="address" rows="2"
                                placeholder="Street, city…"
                                class="{{ $inputClass('address') }} resize-none">{{ old('address') }}</textarea>
                            <x-input-error :messages="$errors->get('address')" class="mt-1.5" />
                        </div>

                        {{-- Divider --}}
                        <div class="border-t border-dashed border-gray-200 pt-2"></div>

                        {{-- Actions --}}
                        <div class="flex items-center justify-end gap-3">
                            <x-button-cancel/>
                            <x-button-submit>
                                <x-icon name="user-add" class="w-4 h-4 mr-1.5" />
                                Create Patient
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
