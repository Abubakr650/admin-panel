<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wider font-medium">Pharmacy › Batches</p>
                <h1 class="text-xl font-bold text-gray-800">New Batch</h1>
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
                            <x-icon name="document" class="w-7 h-7 text-white" />
                        </div>
                        <div>
                            <h2 class="text-white text-xl font-bold">New Pharmacy Batch</h2>
                            <p class="text-sky-100 text-sm mt-0.5">Register a new batch with item and supplier details</p>
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

                    <form action="{{ route('pharmacy.batches.store') }}" method="POST" class="space-y-5"
                        x-data="{ 
                            selectedItemId: '{{ old('pharmacy_item_id') }}',
                            qrInput: '',
                            pharmacyItems: {{ $pharmacyItems->map(fn($i) => ['id' => $i->id, 'qr' => $i->qr_code])->toJson() }},
                            handleScan(code) {
                                const item = this.pharmacyItems.find(i => i.qr === code);
                                if (item) {
                                    this.selectedItemId = item.id;
                                    $dispatch('notify', { message: 'Item selected successfully', type: 'success' });
                                } else {
                                    $dispatch('notify', { message: 'No item found with this QR code', type: 'error' });
                                }
                            }
                        }"
                        x-init="$watch('qrInput', value => { if(value) { handleScan(value); qrInput = ''; } })"
                    >
                        @csrf
                        <input type="hidden" name="idempotency_key" value="{{ Str::uuid() }}">

                        {{-- Item & Supplier --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            {{-- Pharmacy Item --}}
                            <div>
                                <label for="pharmacy_item_id" class="block text-sm font-semibold text-gray-600 mb-1.5">
                                    <span class="flex items-center justify-between w-full">
                                        <span class="flex items-center gap-1.5">
                                            <x-icon name="pill" class="w-4 h-4 text-theme-from" />
                                            Pharmacy Item <span class="text-red-400">*</span>
                                        </span>
                                        <button type="button" x-on:click="$dispatch('open-modal', 'scan-item-qr')"
                                            class="text-[10px] font-bold uppercase tracking-wider text-theme-from hover:text-theme-to transition-colors flex items-center gap-1">
                                            <x-icon name="qr-code" class="w-3 h-3" />
                                            Quick Scan
                                        </button>
                                    </span>
                                </label>
                                <select id="pharmacy_item_id" name="pharmacy_item_id" required
                                    x-model="selectedItemId"
                                    class="{{ $inputClass('pharmacy_item_id') }}">
                                    <option value="">— Select Item —</option>
                                    @foreach($pharmacyItems as $pharmacyItem)
                                        <option value="{{ $pharmacyItem->id }}">
                                            {{ $pharmacyItem->commercial_name }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('pharmacy_item_id')" class="mt-1.5" />

                                {{-- Scan Modal --}}
                                <x-scan-qr-modal 
                                    name="scan-item-qr" 
                                    mode="input" 
                                    target="qrInput" 
                                    title="Scan Item" 
                                    subtitle="Scan item QR to select it" 
                                />
                            </div>

                            {{-- Supplier --}}
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
                                        <option value="{{ $supplier->id }}"
                                            {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                            {{ $supplier->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('supplier_id')" class="mt-1.5" />
                            </div>
                        </div>

                        {{-- Batch Number --}}
                        <div>
                            <label for="batch_number" class="block text-sm font-semibold text-gray-600 mb-1.5">
                                <span class="flex items-center gap-1.5">
                                    <x-icon name="hashtag" class="w-4 h-4 text-theme-from" />
                                    Batch Number <span class="text-red-400">*</span>
                                </span>
                            </label>
                            <input type="text" id="batch_number" name="batch_number"
                                value="{{ old('batch_number') }}" required
                                placeholder="e.g. BTC-2024-001"
                                class="{{ $inputClass('batch_number') }}">
                            <x-input-error :messages="$errors->get('batch_number')" class="mt-1.5" />
                        </div>

                        {{-- Quantity --}}
                        <div>
                            <label for="quantity" class="block text-sm font-semibold text-gray-600 mb-1.5">
                                <span class="flex items-center gap-1.5">
                                    <x-icon name="plus" class="w-4 h-4 text-theme-from" />
                                    Quantity <span class="text-red-400">*</span>
                                </span>
                            </label>
                            <input type="number" id="quantity" name="quantity" min="1"
                                value="{{ old('quantity') }}" required
                                placeholder="e.g. 100"
                                class="{{ $inputClass('quantity') }}">
                            <x-input-error :messages="$errors->get('quantity')" class="mt-1.5" />
                        </div>

                        {{-- Production & Expiry Dates --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            {{-- Production Date --}}
                            <div>
                                <label for="production_date" class="block text-sm font-semibold text-gray-600 mb-1.5">
                                    <span class="flex items-center gap-1.5">
                                        <x-icon name="calendar" class="w-4 h-4 text-theme-from" />
                                        Production Date
                                    </span>
                                </label>
                                <input type="date" id="production_date" name="production_date"
                                    value="{{ old('production_date') }}"
                                    class="{{ $inputClass('production_date') }}">
                                <x-input-error :messages="$errors->get('production_date')" class="mt-1.5" />
                            </div>

                            {{-- Expiry Date --}}
                            <div>
                                <label for="expiry_date" class="block text-sm font-semibold text-gray-600 mb-1.5">
                                    <span class="flex items-center gap-1.5">
                                        <x-icon name="calendar" class="w-4 h-4 text-red-400" />
                                        Expiry Date <span class="text-red-400">*</span>
                                    </span>
                                </label>
                                <input type="date" id="expiry_date" name="expiry_date"
                                    value="{{ old('expiry_date') }}" required
                                    class="{{ $inputClass('expiry_date') }}">
                                <x-input-error :messages="$errors->get('expiry_date')" class="mt-1.5" />
                            </div>
                        </div>

                        <div class="border-t border-dashed border-gray-200 pt-2"></div>

                        <div class="flex items-center justify-end gap-3">
                            <x-button-cancel />
                            <x-button-submit>
                                <x-icon name="document" class="w-4 h-4 mr-1.5" />
                                Create Batch
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
