<div class="fixed inset-x-0 top-6 z-[9999] pointer-events-none flex flex-col items-center gap-3 px-4">
    {{-- Success Message --}}
    @if ($success = session()->pull('success'))
        <div x-data="{ show: true }"
             x-show="show"
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="opacity-0 -translate-y-4 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 -translate-y-4 scale-95"
             x-init="
                if (window.performance && window.performance.getEntriesByType('navigation')[0] && window.performance.getEntriesByType('navigation')[0].type === 'back_forward') {
                    show = false;
                } else {
                    setTimeout(() => show = false, 5000)
                }
             "
             class="pointer-events-auto min-w-[320px] max-w-md bg-white border-l-4 border-green-500 shadow-2xl rounded-xl p-4 flex items-center gap-3 border border-gray-100">
            <div class="flex-shrink-0 w-8 h-8 rounded-full bg-green-50 flex items-center justify-center">
                <x-icon name="check-circle" class="w-5 h-5 text-green-500" />
            </div>
            <div class="flex-1 text-left">
                <p class="text-sm font-bold text-gray-800">Success</p>
                <p class="text-xs text-gray-500 mt-0.5">{{ $success }}</p>
            </div>
            <button @click="show = false" class="text-gray-300 hover:text-gray-400 transition-colors">
                <x-icon name="x" class="w-4 h-4" />
            </button>
        </div>
    @endif

    {{-- Error Message --}}
    @if ($error = session()->pull('error'))
        <div x-data="{ show: true }"
             x-show="show"
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="opacity-0 -translate-y-4 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 -translate-y-4 scale-95"
             x-init="
                if (window.performance && window.performance.getEntriesByType('navigation')[0] && window.performance.getEntriesByType('navigation')[0].type === 'back_forward') {
                    show = false;
                } else {
                    setTimeout(() => show = false, 12000)
                }
             "
             class="pointer-events-auto min-w-[320px] max-w-md bg-white border-l-4 border-red-500 shadow-2xl rounded-xl p-4 flex items-center gap-3 border border-gray-100">
            <div class="flex-shrink-0 w-8 h-8 rounded-full bg-red-50 flex items-center justify-center">
                <x-icon name="exclamation" class="w-5 h-5 text-red-500" />
            </div>
            <div class="flex-1 text-left">
                <p class="text-sm font-bold text-gray-800">Action Blocked</p>
                <p class="text-xs text-gray-500 mt-0.5">{{ $error }}</p>
            </div>
            <button @click="show = false" class="text-gray-300 hover:text-gray-400 transition-colors">
                <x-icon name="x" class="w-4 h-4" />
            </button>
        </div>
    @endif
</div>