@props([
    'name', 
    'title' => 'Scan QR Code', 
    'subtitle' => 'Scan or enter the code to search',
    'mode' => 'search', // 'search' or 'input'
    'action' => null,   // required for search mode
    'target' => null,   // variable name for input mode (e.g. 'qrValue')
    'extraParams' => [] // for search mode hidden fields
])

<x-modal :name="$name" focusable>
    <div class="p-6" x-data="{ tempScan: '' }">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-theme-from/10 flex items-center justify-center">
                    <x-icon name="qr-code" class="w-6 h-6 text-theme-from" />
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-800">{{ $title }}</h2>
                    <p class="text-sm text-gray-400">{{ $subtitle }}</p>
                </div>
            </div>
            <button type="button" x-on:click="$dispatch('close-modal', '{{ $name }}')" class="text-gray-400 hover:text-gray-600 transition-colors">
                <x-icon name="x" class="w-6 h-6" />
            </button>
        </div>

        @if($mode === 'search')
            <form action="{{ $action }}" method="GET" class="space-y-4">
                @foreach($extraParams as $key => $val)
                    <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                @endforeach

                <div class="relative">
                    <input type="text" name="search" autofocus
                        placeholder="Waiting for scanner input..."
                        class="w-full pl-12 pr-4 py-4 bg-gray-50 border-2 border-gray-100 rounded-2xl focus:border-theme-from focus:ring-0 transition-all duration-200 text-xl font-mono tracking-wider">
                    <div class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                        <x-icon name="search" class="w-6 h-6" />
                    </div>
                </div>
            </form>
        @else
            <div class="space-y-4">
                <div class="relative">
                    <input type="text" x-model="tempScan" autofocus
                        x-on:keydown.enter.prevent.stop="
                            setTimeout(() => { 
                                {{ $target }} = tempScan; 
                                $dispatch('close-modal', '{{ $name }}'); 
                            }, 50)"
                        placeholder="Waiting for scanner..."
                        class="w-full pl-12 pr-4 py-4 bg-gray-50 border-2 border-gray-100 rounded-2xl focus:border-theme-from focus:ring-0 transition-all duration-200 text-xl font-mono tracking-wider">
                    <div class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                        <x-icon name="qr-code" class="w-6 h-6" />
                    </div>
                </div>
            </div>
        @endif

        <div class="flex justify-center pt-2">
            <div class="animate-pulse flex items-center gap-2 text-xs font-medium text-emerald-500 bg-emerald-50 px-3 py-1.5 rounded-full border border-emerald-100">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                Scanner Active
            </div>
        </div>
    </div>
</x-modal>
