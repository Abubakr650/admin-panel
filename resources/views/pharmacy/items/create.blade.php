<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wider font-medium">Pharmacy › Items</p>
                <h1 class="text-xl font-bold text-gray-800">New Pharmacy Item</h1>
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
                            <x-icon name="clipboard" class="w-7 h-7 text-white" />
                        </div>
                        <div>
                            <h2 class="text-white text-xl font-bold">Item Registration</h2>
                            <p class="text-sky-100 text-sm mt-0.5">Fill in the details below to add a new pharmacy item</p>
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

                    <form action="{{ route('pharmacy.items.store') }}" method="POST" class="space-y-5">
                        @csrf
                        <input type="hidden" name="idempotency_key" value="{{ Str::uuid() }}">

                        {{-- Commercial Name --}}
                        <div class="group">
                            <label for="commercial_name" class="block text-sm font-semibold text-gray-600 mb-1.5">
                                <span class="flex items-center gap-1.5">
                                    <x-icon name="clipboard" class="w-4 h-4 text-theme-from" />
                                    Commercial Name <span class="text-red-400">*</span>
                                </span>
                            </label>
                            <input type="text" id="commercial_name" name="commercial_name"
                                value="{{ old('commercial_name') }}"
                                required autofocus
                                placeholder="e.g. Panadol"
                                class="{{ $inputClass('commercial_name') }}">
                            <x-input-error :messages="$errors->get('commercial_name')" class="mt-1.5" />
                        </div>

                        {{-- Scientific Name & Company --}}
                        <div class="grid grid-cols-2 gap-4">
                            {{-- Scientific Name --}}
                            <div>
                                <label for="scientific_name" class="block text-sm font-semibold text-gray-600 mb-1.5">
                                    <span class="flex items-center gap-1.5">
                                        <x-icon name="academic-cap" class="w-4 h-4 text-theme-from" />
                                        Scientific Name <span class="text-red-400">*</span>
                                    </span>
                                </label>
                                <input type="text" id="scientific_name" name="scientific_name"
                                    value="{{ old('scientific_name') }}"
                                    required
                                    placeholder="e.g. Paracetamol"
                                    class="{{ $inputClass('scientific_name') }}">
                                <x-input-error :messages="$errors->get('scientific_name')" class="mt-1.5" />
                            </div>

                            {{-- Company Name --}}
                            <div>
                                <label for="company_name" class="block text-sm font-semibold text-gray-600 mb-1.5">
                                    <span class="flex items-center gap-1.5">
                                        <x-icon name="badge-check" class="w-4 h-4 text-theme-from" />
                                        Company Name <span class="text-red-400">*</span>
                                    </span>
                                </label>
                                <input type="text" id="company_name" name="company_name"
                                    value="{{ old('company_name') }}"
                                    required
                                    placeholder="e.g. GSK"
                                    class="{{ $inputClass('company_name') }}">
                                <x-input-error :messages="$errors->get('company_name')" class="mt-1.5" />
                            </div>
                        </div>

                        {{-- Form & Category --}}
                        <div class="grid grid-cols-2 gap-4">
                            {{-- Form (tablet, capsule, etc.) --}}
                            <div>
                                <label for="form" class="block text-sm font-semibold text-gray-600 mb-1.5">
                                    <span class="flex items-center gap-1.5">
                                        <x-icon name="filter" class="w-4 h-4 text-theme-from" />
                                        Form <span class="text-red-400">*</span>
                                    </span>
                                </label>
                                <select id="form" name="form"
                                    required
                                    class="{{ $inputClass('form') }} appearance-none bg-no-repeat cursor-pointer"
                                    style="background-image: url('data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' fill=\'none\' viewBox=\'0 0 24 24\' stroke=\'%236b7280\'%3E%3Cpath stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M19 9l-7 7-7-7\'/%3E%3C/svg%3E'); background-position: right 12px center; background-size: 16px;">
                                    <option value="">— Select form —</option>
                                    @foreach(['tablet' => 'Tablet', 'capsule' => 'Capsule', 'syrup' => 'Syrup', 'cream' => 'Cream', 'ointment' => 'Ointment', 'injection' => 'Injection', 'suspension' => 'Suspension', 'drops' => 'Drops'] as $value => $label)
                                        <option value="{{ $value }}" {{ old('form') === $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('form')" class="mt-1.5" />
                            </div>

                            {{-- Category --}}
                            <div>
                                <label for="category" class="block text-sm font-semibold text-gray-600 mb-1.5">
                                    <span class="flex items-center gap-1.5">
                                        <x-icon name="document" class="w-4 h-4 text-theme-from" />
                                        Category <span class="text-red-400">*</span>
                                    </span>
                                </label>
                                <select id="category" name="category"
                                    required
                                    class="{{ $inputClass('category') }} appearance-none bg-no-repeat cursor-pointer"
                                    style="background-image: url('data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' fill=\'none\' viewBox=\'0 0 24 24\' stroke=\'%236b7280\'%3E%3Cpath stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M19 9l-7 7-7-7\'/%3E%3C/svg%3E'); background-position: right 12px center; background-size: 16px;">
                                    <option value="">— Select category —</option>
                                    @foreach(['medicine' => 'Medicine', 'supplement' => 'Supplement', 'cosmetic' => 'Cosmetic', 'other' => 'Other'] as $value => $label)
                                        <option value="{{ $value }}" {{ old('category') === $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('category')" class="mt-1.5" />
                            </div>
                        </div>

                        {{-- QR Code --}}
                        <div x-data="{ qrValue: '{{ old('qr_code') }}' }">
                            <label for="qr_code" class="block text-sm font-semibold text-gray-600 mb-1.5">
                                <span class="flex items-center justify-between">
                                    <span class="flex items-center gap-1.5">
                                        <x-icon name="qr-code" class="w-4 h-4 text-theme-from" />
                                        QR Code / Barcode
                                    </span>
                                    <button type="button" x-on:click="$dispatch('open-modal', 'scan-form-qr')" 
                                        class="text-[10px] uppercase tracking-wider font-bold text-theme-from hover:text-theme-to transition-colors flex items-center gap-1">
                                        <x-icon name="qr-code" class="w-3 h-3" />
                                        Quick Scan
                                    </button>
                                </span>
                            </label>
                            <div class="relative">
                                <input type="text" id="qr_code" name="qr_code"
                                    x-model="qrValue"
                                    placeholder="Scan or enter item QR code…"
                                    class="{{ $inputClass('qr_code') }}">
                                <div class="absolute right-3 top-1/2 -translate-y-1/2">
                                    <x-icon name="qr-code" class="w-5 h-5 text-gray-300" />
                                </div>
                            </div>
                            <x-input-error :messages="$errors->get('qr_code')" class="mt-1.5" />

                            {{-- Form-specific Scan Modal --}}
                            <x-scan-qr-modal 
                                name="scan-form-qr" 
                                mode="input" 
                                target="qrValue" 
                                title="Scan Item" 
                                subtitle="Scanner will auto-fill the field" 
                            />
                        </div>

                        {{-- Location in Pharmacy --}}
                        <div>
                            <label for="location_in_pharmacy" class="block text-sm font-semibold text-gray-600 mb-1.5">
                                <span class="flex items-center gap-1.5">
                                    <x-icon name="location" class="w-4 h-4 text-theme-from" />
                                    Location in Pharmacy
                                </span>
                            </label>
                            <input type="text" id="location_in_pharmacy" name="location_in_pharmacy"
                                value="{{ old('location_in_pharmacy') }}"
                                placeholder="e.g. Shelf A3"
                                class="{{ $inputClass('location_in_pharmacy') }}">
                            <x-input-error :messages="$errors->get('location_in_pharmacy')" class="mt-1.5" />
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
                                placeholder="Additional notes about this item…"
                                class="{{ $inputClass('notes') }} resize-none">{{ old('notes') }}</textarea>
                            <x-input-error :messages="$errors->get('notes')" class="mt-1.5" />
                        </div>

                        {{-- Divider --}}
                        <div class="border-t border-dashed border-gray-200 pt-2"></div>

                        {{-- Actions --}}
                        <div class="flex items-center justify-end gap-3">
                            <x-button-cancel/>
                            <x-button-submit>
                                <x-icon name="plus" class="w-4 h-4 mr-1.5" />
                                Create Item
                            </x-button-submit>
                        </div>

                    </form>
                </div>
            </div>

            {{-- Hint --}}
            <p class="text-center text-xs text-gray-400 mt-4 flex items-center justify-center gap-1">
                <x-icon name="info" class="w-3.5 h-3.5 text-theme-from" />
                Fields marked with <span class="text-red-400 font-medium mx-0.5">*</span> are required
            </p>

        </div>
    </div>
</x-app-layout>
