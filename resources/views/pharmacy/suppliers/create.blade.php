<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wider font-medium">Pharmacy › Suppliers</p>
                <h1 class="text-xl font-bold text-gray-800">New Supplier</h1>
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
                            <x-icon name="truck" class="w-7 h-7 text-white" />
                        </div>
                        <div>
                            <h2 class="text-white text-xl font-bold">New Supplier</h2>
                            <p class="text-sky-100 text-sm mt-0.5">Fill in the details below to register a new supplier</p>
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

                    <form action="{{ route('pharmacy.suppliers.store') }}" method="POST" class="space-y-5">
                        @csrf
                        <input type="hidden" name="idempotency_key" value="{{ Str::uuid() }}">

                        {{-- Supplier Name --}}
                        <div class="group">
                            <label for="name" class="block text-sm font-semibold text-gray-600 mb-1.5">
                                <span class="flex items-center gap-1.5">
                                    <x-icon name="truck" class="w-4 h-4 text-theme-from" />
                                    Supplier Name <span class="text-red-400">*</span>
                                </span>
                            </label>
                            <input type="text" id="name" name="name"
                                value="{{ old('name') }}" required autofocus
                                placeholder="e.g. MedPharm Co."
                                class="{{ $inputClass('name') }}">
                            <x-input-error :messages="$errors->get('name')" class="mt-1.5" />
                        </div>

                        {{-- Phone & Email --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            {{-- Phone --}}
                            <div>
                                <label for="phone" class="block text-sm font-semibold text-gray-600 mb-1.5">
                                    <span class="flex items-center gap-1.5">
                                        <x-icon name="phone" class="w-4 h-4 text-theme-from" />
                                        Phone
                                    </span>
                                </label>
                                <input type="text" id="phone" name="phone"
                                    value="{{ old('phone') }}"
                                    placeholder="+967 7xx xxx xxx"
                                    class="{{ $inputClass('phone') }}">
                                <x-input-error :messages="$errors->get('phone')" class="mt-1.5" />
                            </div>

                            {{-- Email --}}
                            <div>
                                <label for="email" class="block text-sm font-semibold text-gray-600 mb-1.5">
                                    <span class="flex items-center gap-1.5">
                                        <x-icon name="mail" class="w-4 h-4 text-theme-from" />
                                        Email
                                    </span>
                                </label>
                                <input type="email" id="email" name="email"
                                    value="{{ old('email') }}"
                                    placeholder="supplier@example.com"
                                    class="{{ $inputClass('email') }}">
                                <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
                            </div>
                        </div>

                        {{-- Country --}}
                        <div>
                            <label for="country" class="block text-sm font-semibold text-gray-600 mb-1.5">
                                <span class="flex items-center gap-1.5">
                                    <x-icon name="location" class="w-4 h-4 text-theme-from" />
                                    Country
                                </span>
                            </label>
                            <input type="text" id="country" name="country"
                                value="{{ old('country') }}"
                                placeholder="e.g. Yemen"
                                class="{{ $inputClass('country') }}">
                            <x-input-error :messages="$errors->get('country')" class="mt-1.5" />
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

                        {{-- Notes --}}
                        <div>
                            <label for="notes" class="block text-sm font-semibold text-gray-600 mb-1.5">
                                <span class="flex items-center gap-1.5">
                                    <x-icon name="document" class="w-4 h-4 text-theme-from" />
                                    Notes
                                </span>
                            </label>
                            <textarea id="notes" name="notes" rows="3"
                                placeholder="Additional notes about this supplier…"
                                class="{{ $inputClass('notes') }} resize-none">{{ old('notes') }}</textarea>
                            <x-input-error :messages="$errors->get('notes')" class="mt-1.5" />
                        </div>

                        <div class="border-t border-dashed border-gray-200 pt-2"></div>

                        <div class="flex items-center justify-end gap-3">
                            <x-button-cancel />
                            <x-button-submit>
                                <x-icon name="truck" class="w-4 h-4 mr-1.5" />
                                Create Supplier
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
