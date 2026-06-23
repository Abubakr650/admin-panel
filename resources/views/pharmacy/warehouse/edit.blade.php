<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wider font-medium">Pharmacy › Warehouse</p>
                <h1 class="text-xl font-bold text-gray-800">Edit Inventory Item</h1>
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
                            <x-icon name="edit" class="w-7 h-7 text-white" />
                        </div>
                        <div>
                            <h2 class="text-white text-xl font-bold">Edit Item</h2>
                            <p class="text-sky-100 text-sm mt-0.5">{{ $item->name }}</p>
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

                    <form action="{{ route('pharmacy.warehouse.update', $item->id) }}" method="POST" class="space-y-5">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="idempotency_key" value="{{ Str::uuid() }}">

                        {{-- Name --}}
                        <div>
                            <label for="name" class="block text-sm font-semibold text-gray-600 mb-1.5">
                                <span class="flex items-center gap-1.5">
                                    <x-icon name="pill" class="w-4 h-4 text-theme-from" />
                                    Item Name <span class="text-red-400">*</span>
                                </span>
                            </label>
                            <input type="text" id="name" name="name"
                                value="{{ old('name', $item->name) }}" required autofocus
                                placeholder="Enter item name..."
                                class="{{ $inputClass('name') }}">
                            <x-input-error :messages="$errors->get('name')" class="mt-1.5" />
                        </div>

                        {{-- Company & Category --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="company_name" class="block text-sm font-semibold text-gray-600 mb-1.5">
                                    <span class="flex items-center gap-1.5">
                                        <x-icon name="library" class="w-4 h-4 text-theme-from" />
                                        Company / Laboratory
                                    </span>
                                </label>
                                <input type="text" id="company_name" name="company_name"
                                    value="{{ old('company_name', $item->company_name) }}"
                                    placeholder="Enter company name..."
                                    class="{{ $inputClass('company_name') }}">
                                <x-input-error :messages="$errors->get('company_name')" class="mt-1.5" />
                            </div>

                            <div>
                                <label for="category" class="block text-sm font-semibold text-gray-600 mb-1.5">
                                    <span class="flex items-center gap-1.5">
                                        <x-icon name="document" class="w-4 h-4 text-theme-from" />
                                        Category
                                    </span>
                                </label>
                                <input type="text" id="category" name="category"
                                    value="{{ old('category', $item->category) }}"
                                    placeholder="e.g. Capsules, Syrup..."
                                    class="{{ $inputClass('category') }}">
                                <x-input-error :messages="$errors->get('category')" class="mt-1.5" />
                            </div>
                        </div>

                        {{-- Quantity & Type --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="quantity" class="block text-sm font-semibold text-gray-600 mb-1.5">
                                    <span class="flex items-center gap-1.5">
                                        <x-icon name="plus" class="w-4 h-4 text-theme-from" />
                                        Quantity <span class="text-red-400">*</span>
                                    </span>
                                </label>
                                <input type="number" id="quantity" name="quantity" min="0" required
                                    value="{{ old('quantity', $item->quantity) }}"
                                    placeholder="0"
                                    class="{{ $inputClass('quantity') }}">
                                <x-input-error :messages="$errors->get('quantity')" class="mt-1.5" />
                            </div>

                            <div>
                                <label for="type" class="block text-sm font-semibold text-gray-600 mb-1.5">
                                    <span class="flex items-center gap-1.5">
                                        <x-icon name="info" class="w-4 h-4 text-theme-from" />
                                        Unit Type
                                    </span>
                                </label>
                                <input type="text" id="type" name="type"
                                    value="{{ old('type', $item->type) }}"
                                    placeholder="e.g. Box, Bottle..."
                                    class="{{ $inputClass('type') }}">
                                <x-input-error :messages="$errors->get('type')" class="mt-1.5" />
                            </div>
                        </div>

                        {{-- Supplier & Location --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="supplier_id" class="block text-sm font-semibold text-gray-600 mb-1.5">
                                    <span class="flex items-center gap-1.5">
                                        <x-icon name="truck" class="w-4 h-4 text-theme-from" />
                                        Supplier
                                    </span>
                                </label>
                                <select id="supplier_id" name="supplier_id"
                                    class="{{ $inputClass('supplier_id') }}">
                                    <option value="">— Select Supplier —</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}" {{ old('supplier_id', $item->supplier_id) == $supplier->id ? 'selected' : '' }}>
                                            {{ $supplier->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('supplier_id')" class="mt-1.5" />
                            </div>

                            <div>
                                <label for="location_in_warehouse" class="block text-sm font-semibold text-gray-600 mb-1.5">
                                    <span class="flex items-center gap-1.5">
                                        <x-icon name="location" class="w-4 h-4 text-theme-from" />
                                        Storage Location
                                    </span>
                                </label>
                                <input type="text" id="location_in_warehouse" name="location_in_warehouse"
                                    value="{{ old('location_in_warehouse', $item->location_in_warehouse) }}"
                                    placeholder="e.g. Rack A1, Drawer 2..."
                                    class="{{ $inputClass('location_in_warehouse') }}">
                                <x-input-error :messages="$errors->get('location_in_warehouse')" class="mt-1.5" />
                            </div>
                        </div>

                        {{-- QR Code --}}
                        <div x-data="{ qrValue: '{{ old('qr_code', $item->qr_code) }}' }">
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

                        {{-- Production & Expiry Dates --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="production_date" class="block text-sm font-semibold text-gray-600 mb-1.5">
                                    <span class="flex items-center gap-1.5">
                                        <x-icon name="calendar" class="w-4 h-4 text-theme-from" />
                                        Production Date
                                    </span>
                                </label>
                                <input type="date" id="production_date" name="production_date"
                                    value="{{ old('production_date', $item->production_date?->format('Y-m-d')) }}"
                                    class="{{ $inputClass('production_date') }}">
                                <x-input-error :messages="$errors->get('production_date')" class="mt-1.5" />
                            </div>

                            <div>
                                <label for="expiry_date" class="block text-sm font-semibold text-gray-600 mb-1.5">
                                    <span class="flex items-center gap-1.5">
                                        <x-icon name="calendar" class="w-4 h-4 text-red-500" />
                                        Expiry Date <span class="text-red-400">*</span>
                                    </span>
                                </label>
                                <input type="date" id="expiry_date" name="expiry_date" required
                                    value="{{ old('expiry_date', $item->expiry_date?->format('Y-m-d')) }}"
                                    class="{{ $inputClass('expiry_date') }}">
                                <x-input-error :messages="$errors->get('expiry_date')" class="mt-1.5" />
                            </div>
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
                                placeholder="Add any additional notes here..."
                                class="{{ $inputClass('notes') }} resize-none">{{ old('notes', $item->notes) }}</textarea>
                            <x-input-error :messages="$errors->get('notes')" class="mt-1.5" />
                        </div>

                        <div class="border-t border-dashed border-gray-200 pt-2"></div>

                        <div class="flex items-center justify-end gap-3">
                            <x-button-cancel />
                            <x-button-submit>
                                <x-icon name="check-circle" class="w-4 h-4 mr-1.5" />
                                Save Changes
                            </x-button-submit>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
