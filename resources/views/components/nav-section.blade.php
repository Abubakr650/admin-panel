@props([
    'title',
    'active' => false,
])

<li
    x-data="{
        open: {{ $active ? 'true' : 'false' }},
        get isCollapsed() { return !isMobile() && !sidebarOpen; }
    }"
    class="w-full"
>

    {{-- Toggle Button --}}
    <button
        @click="if (isCollapsed) { sidebarOpen = true; open = true; } else { open = !open; }"
        :title="isCollapsed ? '{{ $title }}' : ''"
        class="flex items-center w-full px-3 py-2 rounded-lg text-sm font-medium transition duration-150 ease-in-out focus:outline-none
               {{ $active
                   ? 'text-theme-from bg-theme-from/5 hover:bg-theme-from/10'
                   : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}"
    >
        {{-- Section Icon --}}
        @isset($icon)
            <span class="w-5 h-5 shrink-0 {{ $active ? 'text-theme-from' : 'text-gray-400' }}">
                {{ $icon }}
            </span>
        @endisset

        {{-- Title (hidden in mini mode) --}}
        <span
            class="ml-3 flex-1 text-left whitespace-nowrap overflow-hidden transition-all duration-300"
            :class="isCollapsed ? 'opacity-0 w-0 ml-0' : 'opacity-100'"
        >
            {{ $title }}
        </span>

        {{-- Chevron (hidden in mini mode) --}}
        <svg
            class="w-4 h-4 shrink-0 transition-all duration-200 {{ $active ? 'text-theme-from/60' : 'text-gray-300' }}"
            :class="{
                'rotate-180': open,
                'opacity-0 w-0': isCollapsed,
                'opacity-100': !isCollapsed
            }"
            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
            stroke="currentColor" stroke-width="2"
        >
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    {{-- Collapsible Sub-Links (hidden in mini mode) --}}
    <ul
        x-show="open && !isCollapsed"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 -translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-1"
        class="mt-0.5 ml-3 pl-3 border-l border-gray-200 space-y-0.5"
    >
        {{ $slot }}
    </ul>

</li>
