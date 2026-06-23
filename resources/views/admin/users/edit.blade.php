<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wider font-medium">Admin › Users</p>
                <h1 class="text-xl font-bold text-gray-800">Edit User</h1>
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
                            <x-icon name="edit" class="w-7 h-7 text-white" />
                        </div>
                        <div>
                            <h2 class="text-white text-xl font-bold">Update User Account</h2>
                            <p class="text-sky-100 text-sm mt-0.5">Review and update the user's information below</p>
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
                        $dropdownBg = "background-image: url('data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' fill=\'none\' viewBox=\'0 0 24 24\' stroke=\'%236b7280\'%3E%3Cpath stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M19 9l-7 7-7-7\'/%3E%3C/svg%3E'); background-position: right 12px center; background-size: 16px;";
                    @endphp

                    <form action="{{ route('users.update', $user->id) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="idempotency_key" value="{{ Str::uuid() }}">

                        {{-- Current Avatar --}}
                        @if($user->image)
                        <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-xl border border-gray-200">
                            <img src="{{ $user->image_url }}" alt="{{ $user->name }}"
                                 class="w-14 h-14 rounded-xl object-cover border border-gray-200 shadow-sm">
                            <div>
                                <p class="text-sm font-semibold text-gray-700">Current Photo</p>
                                <p class="text-xs text-gray-400 mt-0.5">Upload below to replace</p>
                            </div>
                        </div>
                        @endif

                        {{-- Username & Full Name --}}
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="name" class="block text-sm font-semibold text-gray-600 mb-1.5">
                                    <span class="flex items-center gap-1.5">
                                        <x-icon name="user" class="w-4 h-4 text-theme-from" />
                                        Username <span class="text-red-400">*</span>
                                    </span>
                                </label>
                                <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}"
                                    required autofocus class="{{ $inputClass('name') }}">
                                <x-input-error :messages="$errors->get('name')" class="mt-1.5" />
                            </div>
                            <div>
                                <label for="full_name" class="block text-sm font-semibold text-gray-600 mb-1.5">
                                    <span class="flex items-center gap-1.5">
                                        <x-icon name="user" class="w-4 h-4 text-theme-from" />
                                        Full Name
                                    </span>
                                </label>
                                <input type="text" id="full_name" name="full_name" value="{{ old('full_name', $user->full_name) }}"
                                    class="{{ $inputClass('full_name') }}">
                                <x-input-error :messages="$errors->get('full_name')" class="mt-1.5" />
                            </div>
                        </div>

                        {{-- Email --}}
                        <div>
                            <label for="email" class="block text-sm font-semibold text-gray-600 mb-1.5">
                                <span class="flex items-center gap-1.5">
                                    <x-icon name="mail" class="w-4 h-4 text-theme-from" />
                                    Email <span class="text-red-400">*</span>
                                </span>
                            </label>
                            <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}"
                                required class="{{ $inputClass('email') }}">
                            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
                        </div>

                        {{-- Password (optional on edit) --}}
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="password" class="block text-sm font-semibold text-gray-600 mb-1.5">
                                    <span class="flex items-center gap-1.5">
                                        <x-icon name="info" class="w-4 h-4 text-theme-from" />
                                        New Password
                                    </span>
                                </label>
                                <input type="password" id="password" name="password"
                                    placeholder="Leave blank to keep current"
                                    class="{{ $inputClass('password') }}">
                                <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
                            </div>
                            <div>
                                <label for="password_confirmation" class="block text-sm font-semibold text-gray-600 mb-1.5">
                                    <span class="flex items-center gap-1.5">
                                        <x-icon name="info" class="w-4 h-4 text-theme-from" />
                                        Confirm Password
                                    </span>
                                </label>
                                <input type="password" id="password_confirmation" name="password_confirmation"
                                    placeholder="Repeat new password"
                                    class="{{ $inputClass('password_confirmation') }}">
                            </div>
                        </div>

                        {{-- Role & Gender --}}
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="role" class="block text-sm font-semibold text-gray-600 mb-1.5">
                                    <span class="flex items-center gap-1.5">
                                        <x-icon name="badge-check" class="w-4 h-4 text-theme-from" />
                                        Role <span class="text-red-400">*</span>
                                    </span>
                                </label>
                                <div class="relative">
                                    <select id="role_display" disabled
                                        class="{{ $inputClass('role') }} opacity-60 cursor-not-allowed appearance-none bg-no-repeat"
                                        style="{{ $dropdownBg }}">
                                        @foreach($rolesAvailable as $role)
                                            <option value="{{ $role }}" {{ old('role', $user->role) === $role ? 'selected' : '' }}>
                                                {{ ucwords(str_replace('-', ' ', $role)) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="role" value="{{ $user->role }}">
                                </div>
                                <p class="text-[10px] text-gray-400 mt-1 ml-1 italic">Role cannot be changed after creation</p>
                                <x-input-error :messages="$errors->get('role')" class="mt-1.5" />
                            </div>
                            <div>
                                <label for="gender" class="block text-sm font-semibold text-gray-600 mb-1.5">
                                    <span class="flex items-center gap-1.5">
                                        <x-icon name="gender" class="w-4 h-4 text-theme-from" />
                                        Gender <span class="text-red-400">*</span>
                                    </span>
                                </label>
                                <select id="gender" name="gender" required
                                    class="{{ $inputClass('gender') }} appearance-none bg-no-repeat cursor-pointer"
                                    style="{{ $dropdownBg }}">
                                    <option value="male"   {{ old('gender', $user->gender) === 'male'   ? 'selected' : '' }}>♂ Male</option>
                                    <option value="female" {{ old('gender', $user->gender) === 'female' ? 'selected' : '' }}>♀ Female</option>
                                </select>
                                <x-input-error :messages="$errors->get('gender')" class="mt-1.5" />
                            </div>
                        </div>

                        {{-- Phone & Birth Date --}}
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="phone" class="block text-sm font-semibold text-gray-600 mb-1.5">
                                    <span class="flex items-center gap-1.5">
                                        <x-icon name="phone" class="w-4 h-4 text-theme-from" />
                                        Phone
                                    </span>
                                </label>
                                <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}"
                                    class="{{ $inputClass('phone') }}">
                                <x-input-error :messages="$errors->get('phone')" class="mt-1.5" />
                            </div>
                            <div>
                                <label for="birth_date" class="block text-sm font-semibold text-gray-600 mb-1.5">
                                    <span class="flex items-center gap-1.5">
                                        <x-icon name="calendar" class="w-4 h-4 text-theme-from" />
                                        Birth Date
                                    </span>
                                </label>
                                <input type="date" id="birth_date" name="birth_date"
                                    value="{{ old('birth_date', $user->birth_date?->format('Y-m-d')) }}"
                                    class="{{ $inputClass('birth_date') }}">
                                <x-input-error :messages="$errors->get('birth_date')" class="mt-1.5" />
                            </div>
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
                                class="{{ $inputClass('address') }} resize-none">{{ old('address', $user->address) }}</textarea>
                            <x-input-error :messages="$errors->get('address')" class="mt-1.5" />
                        </div>

                        {{-- Profile Photo --}}
                        <div>
                            <label for="image" class="block text-sm font-semibold text-gray-600 mb-1.5">
                                <span class="flex items-center gap-1.5">
                                    <x-icon name="user" class="w-4 h-4 text-theme-from" />
                                    Replace Profile Photo
                                </span>
                            </label>
                            <input type="file" id="image" name="image" accept="image/*"
                                class="w-full text-sm text-gray-600 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:font-semibold file:bg-theme-from/10 file:text-theme-from hover:file:bg-theme-from/20 transition-all duration-200 border border-gray-200 bg-gray-50 rounded-xl px-3 py-2">
                            <x-input-error :messages="$errors->get('image')" class="mt-1.5" />
                        </div>

                        <div class="border-t border-dashed border-gray-200 pt-2"></div>
                        <div class="flex items-center justify-end gap-3">
                            <x-button-cancel/>
                            <x-button-submit>
                                <x-icon name="user-add" class="w-4 h-4 mr-1.5" />
                                Save Changes
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
</x-app-layout>
