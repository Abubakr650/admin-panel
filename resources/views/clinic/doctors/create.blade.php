<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wider font-medium">Clinic › Doctors</p>
                <h1 class="text-xl font-bold text-gray-800">New Doctor</h1>
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
                            <h2 class="text-white text-xl font-bold">Doctor Registration</h2>
                            <p class="text-sky-100 text-sm mt-0.5">Fill in the details below to register a new doctor</p>
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

                    <form action="{{ route('doctors.store') }}" method="POST" class="space-y-5"
                          x-data="{
                            specialty: '{{ old('specialty', request('specialty', '')) }}',
                            degree: '{{ old('degree', request('degree', '')) }}',
                            is_active: {{ old('is_active', request('is_active', '1')) }},
                            userId: '{{ old('user_id', request('user_id', '')) }}',
                            get userCreateUrl() {
                                let params = new URLSearchParams({
                                    return_to: '{{ route('doctors.create') }}',
                                    specialty: this.specialty,
                                    degree: this.degree,
                                    is_active: this.is_active ? 1 : 0,
                                    role: 'doctor'
                                });
                                return '{{ route('users.create') }}?' + params.toString();
                            }
                          }">
                        @csrf
                        {{-- Idempotency Key --}}
                        <input type="hidden" name="idempotency_key" value="{{ Str::uuid() }}">

                        {{-- User Account --}}
                        <div class="group">
                            <label for="user_id" class="block text-sm font-semibold text-gray-600 mb-1.5">
                                <span class="flex items-center gap-1.5">
                                    <x-icon name="user" class="w-4 h-4 text-theme-from" />
                                    User Account <span class="text-red-400">*</span>
                                </span>
                            </label>
                            <div class="flex items-end gap-2">
                                <div class="flex-1">
                                    <select id="user_id" name="user_id" required
                                        x-model="userId"
                                        class="{{ $inputClass('user_id') }} appearance-none bg-no-repeat cursor-pointer"
                                        style="background-image: url('data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' fill=\'none\' viewBox=\'0 0 24 24\' stroke=\'%236b7280\'%3E%3Cpath stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M19 9l-7 7-7-7\'/%3E%3C/svg%3E'); background-position: right 12px center; background-size: 16px;">
                                        <option value="" disabled>Select User (Account)</option>
                                        @foreach($users ?? [] as $user)
                                            <option value="{{ $user->id }}" {{ (old('user_id', request('user_id')) == $user->id) ? 'selected' : '' }}>{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <a :href="userCreateUrl"
                                   title="Quick Add User"
                                   class="shrink-0 w-11 h-11 flex items-center justify-center bg-theme-from/10 text-theme-from rounded-xl hover:bg-theme-from hover:text-white transition-all duration-200 shadow-sm border border-theme-from/20">
                                    <x-icon name="plus" class="w-5 h-5" />
                                </a>
                            </div>
                            <x-input-error :messages="$errors->get('user_id')" class="mt-1.5" />
                        </div>

                        {{-- Specialty & Degree --}}
                        <div class="grid grid-cols-2 gap-4">
                            {{-- Specialty --}}
                            <div>
                                <label for="specialty" class="block text-sm font-semibold text-gray-600 mb-1.5">
                                    <span class="flex items-center gap-1.5">
                                        <x-icon name="stethoscope" class="w-4 h-4 text-theme-from" />
                                        Specialty <span class="text-red-400">*</span>
                                    </span>
                                </label>
                                <input type="text" id="specialty" name="specialty"
                                    x-model="specialty"
                                    required autofocus
                                    placeholder="e.g. Orthodontics"
                                    class="{{ $inputClass('specialty') }}">
                                <x-input-error :messages="$errors->get('specialty')" class="mt-1.5" />
                            </div>

                            {{-- Degree --}}
                            <div>
                                <label for="degree" class="block text-sm font-semibold text-gray-600 mb-1.5">
                                    <span class="flex items-center gap-1.5">
                                        <x-icon name="academic-cap" class="w-4 h-4 text-theme-from" />
                                        Degree
                                    </span>
                                </label>
                                <input type="text" id="degree" name="degree"
                                    x-model="degree"
                                    placeholder="e.g. DDS, BDS"
                                    class="{{ $inputClass('degree') }}">
                                <x-input-error :messages="$errors->get('degree')" class="mt-1.5" />
                            </div>
                        </div>

                        {{-- Active Status --}}
                        <div class="flex items-center gap-3 p-3.5 rounded-xl bg-gray-50 border border-gray-200">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" id="is_active" name="is_active" value="1"
                                @change="is_active = $event.target.checked"
                                :checked="is_active"
                                class="w-4 h-4 rounded text-theme-from focus:ring-theme-from border-gray-300 cursor-pointer">
                            <label for="is_active" class="text-sm font-semibold text-gray-700 cursor-pointer select-none">
                                Active Doctor
                            </label>
                            <span class="text-xs text-gray-400 ml-auto">Toggle to set availability</span>
                        </div>

                        {{-- Divider --}}
                        <div class="border-t border-dashed border-gray-200 pt-2"></div>

                        {{-- Actions --}}
                        <div class="flex items-center justify-end gap-3">
                            <x-button-cancel/>
                            <x-button-submit>
                                <x-icon name="user-add" class="w-4 h-4 mr-1.5" />
                                Create Doctor
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
