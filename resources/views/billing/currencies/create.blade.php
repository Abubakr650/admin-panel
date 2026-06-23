<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-theme-from/10 text-theme-from shrink-0">
                <x-icon name="currency-dollar" class="w-5 h-5" />
            </div>
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-widest font-semibold">Billing › Currencies</p>
                <h1 class="text-xl font-bold text-gray-800">New Currency</h1>
            </div>
        </div>
    </x-slot>

    <div class="p-6">
        <div class="max-w-2xl mx-auto">
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
                <div class="bg-gradient-to-r from-theme-from to-theme-to px-8 py-6">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center shadow-inner">
                            <x-icon name="currency-dollar" class="w-7 h-7 text-white" />
                        </div>
                        <div>
                            <h2 class="text-white text-xl font-bold">Add Currency</h2>
                            <p class="text-sky-100 text-sm mt-0.5">Define a new currency and its exchange rate</p>
                        </div>
                    </div>
                </div>

                <div class="px-8 py-7">
                    @php
                        $inputClass = fn($f) => 'w-full px-4 py-2.5 rounded-xl border text-gray-800 text-sm placeholder-gray-400 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-theme-from/40 focus:border-theme-from ' . ($errors->has($f) ? 'border-red-400 bg-red-50' : 'border-gray-200 bg-gray-50 hover:border-theme-from');
                    @endphp

                    <form action="{{ route('currencies.store') }}" method="POST" class="space-y-6">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Currency Name <span class="text-red-500">*</span></label>
                                <input type="text" name="name" value="{{ old('name') }}" placeholder="e.g. Yemeni Rial" required class="{{ $inputClass('name') }}">
                                @error('name')<p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Currency Code <span class="text-red-500">*</span></label>
                                <input type="text" name="code" value="{{ old('code') }}" placeholder="e.g. YER" maxlength="10" required class="{{ $inputClass('code') }} uppercase font-mono tracking-wider">
                                @error('code')<p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        <div class="pt-4 border-t border-gray-100">
                            <h3 class="text-sm font-bold text-gray-800 mb-4 flex items-center gap-2">
                                <svg class="w-4 h-4 text-theme-from" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" /></svg>
                                Exchange Rate Setup
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-gray-50/50 p-4 rounded-xl border border-gray-100">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Base Currency</label>
                                    <select name="base_currency_id" class="{{ $inputClass('base_currency_id') }}">
                                        <option value="">-- None --</option>
                                        @foreach($currencies as $c)
                                            <option value="{{ $c->id }}" {{ old('base_currency_id') == $c->id ? 'selected' : '' }}>{{ $c->code }} - {{ $c->name }}</option>
                                        @endforeach
                                    </select>
                                    <p class="text-xs text-gray-400 mt-1.5">Select a currency to define the rate against.</p>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Exchange Rate (1 Base = ?)</label>
                                    <input type="number" step="0.000001" name="exchange_rate" value="{{ old('exchange_rate') }}" placeholder="e.g. 530" class="{{ $inputClass('exchange_rate') }}">
                                    @error('exchange_rate')<p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p>@enderror
                                    <p class="text-xs text-gray-400 mt-1.5">Value of 1 Base Currency in this new currency.</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 pt-6 border-t border-gray-100">
                            <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-theme-from to-theme-to text-white text-sm font-bold rounded-xl hover:opacity-90 transition-all shadow-md flex items-center gap-2">
                                <x-icon name="check-circle" class="w-4 h-4" />
                                Save Currency
                            </button>
                            <a href="{{ route('currencies.index') }}" class="px-6 py-2.5 bg-gray-100 text-gray-700 text-sm font-bold rounded-xl hover:bg-gray-200 transition-all">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
