<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-theme-from/10 text-theme-from shrink-0">
                <x-icon name="lightning-bolt" class="w-5 h-5" />
            </div>
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-widest font-semibold">Billing › Services</p>
                <h1 class="text-xl font-bold text-gray-800">New Service</h1>
            </div>
        </div>
    </x-slot>
    <div class="p-6">
        <div class="max-w-2xl mx-auto">
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
                <div class="bg-gradient-to-r from-theme-from to-theme-to px-8 py-6">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center">
                            <x-icon name="lightning-bolt" class="w-7 h-7 text-white" />
                        </div>
                        <div>
                            <h2 class="text-white text-xl font-bold">Add Service</h2>
                            <p class="text-sky-100 text-sm mt-0.5">Define a treatment service with default pricing</p>
                        </div>
                    </div>
                </div>
                <div class="px-8 py-7">
                    @php
                        $ic = fn($f) => 'w-full px-4 py-2.5 rounded-xl border text-gray-800 text-sm placeholder-gray-400 transition-all focus:outline-none focus:ring-2 focus:ring-theme-from/40 focus:border-theme-from ' . ($errors->has($f) ? 'border-red-400 bg-red-50' : 'border-gray-200 bg-gray-50 hover:border-theme-from');
                    @endphp
                    <form action="{{ route('services.store') }}" method="POST" class="space-y-5">
                        @csrf
                        <div class="grid grid-cols-2 gap-4">
                            <div class="col-span-2">
                                <label class="block text-sm font-semibold text-gray-600 mb-1.5">Service Name <span class="text-red-400">*</span></label>
                                <input type="text" name="name" value="{{ old('name') }}" placeholder="e.g. Dental Filling, Scaling…"
                                    class="{{ $ic('name') }}" autofocus>
                                <x-input-error :messages="$errors->get('name')" class="mt-1" />
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-600 mb-1.5">Code (optional)</label>
                                <input type="text" name="code" value="{{ old('code') }}" placeholder="e.g. FILL-01"
                                    class="{{ $ic('code') }}">
                                <x-input-error :messages="$errors->get('code')" class="mt-1" />
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-600 mb-1.5">Default Price ($) <span class="text-red-400">*</span></label>
                                <input type="number" name="default_price" value="{{ old('default_price') }}" step="0.01" min="0" placeholder="0.00"
                                    class="{{ $ic('default_price') }}">
                                <x-input-error :messages="$errors->get('default_price')" class="mt-1" />
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-600 mb-1.5">Duration (minutes)</label>
                                <input type="number" name="duration_minutes" value="{{ old('duration_minutes') }}" min="0" placeholder="30"
                                    class="{{ $ic('duration_minutes') }}">
                            </div>
                            <div class="flex items-center gap-3 pt-2">
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <div class="relative">
                                        <input type="checkbox" name="is_active" value="1" class="sr-only peer" {{ old('is_active', '1') ? 'checked' : '' }}>
                                        <div class="w-11 h-6 bg-gray-200 rounded-full peer-checked:bg-theme-from transition-all"></div>
                                        <div class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow transition-all peer-checked:translate-x-5"></div>
                                    </div>
                                    <span class="text-sm font-semibold text-gray-600">Active (visible for treatments)</span>
                                </label>
                            </div>
                        </div>
                        <div class="border-t border-dashed border-gray-200 pt-2 flex items-center justify-end gap-3">
                            <x-button-cancel/>
                            <x-button-submit><x-icon name="check-circle" class="w-4 h-4 mr-1.5" /> Create Service</x-button-submit>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
