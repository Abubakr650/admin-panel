<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Ibtisama-System') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            [x-cloak] { display: none !important; }
            /* تيم معدل بتيلويند - Tailwind Dynamic Theme Variables */
            {!! $themeCss !!}
        </style>
    </head>

    <body class="font-sans antialiased bg-gray-100">

        {{--
            Root Alpine.js context:
            - sidebarOpen : controls desktop sidebar (expanded vs collapsed/mini)
            - mobileOpen  : controls mobile drawer overlay
        --}}
        <div
            class="flex h-screen overflow-hidden"
            x-data="{
                sidebarOpen: window.innerWidth >= 1024 && localStorage.getItem('sidebarOpen') !== 'false',
                mobileOpen: false,
                isMobile() { return window.innerWidth < 1024; }
            }"
            x-init="$watch('sidebarOpen', function(val) { localStorage.setItem('sidebarOpen', val) })"
            @resize.window="
                if (window.innerWidth >= 1024) { mobileOpen = false; }
            "
        >

            {{-- ── Mobile Backdrop Overlay ──────────────────────────────── --}}
            <div
                x-show="mobileOpen"
                x-cloak
                @click="mobileOpen = false"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 z-20 bg-gray-900/50 lg:hidden"
            ></div>

            {{-- ── Sidebar ───────────────────────────────────────────────── --}}
            {{-- Desktop: shrinks to 64px (icon-only) when sidebarOpen=false  --}}
            {{-- Mobile: slides in as a fixed drawer controlled by mobileOpen  --}}
            <aside
                {{-- Desktop logic --}}
                :class="isMobile()
                    ? ''
                    : (sidebarOpen ? 'w-[250px]' : 'w-[64px]')"

                {{-- Mobile logic: fixed drawer --}}
                class="relative z-30 flex-shrink-0 transition-all duration-300 ease-in-out
                       hidden lg:flex lg:flex-col"

                {{-- Show/hide on mobile via separate drawer --}}
            >
                @include('layouts.navigation')
            </aside>

            {{-- Mobile Drawer (separate fixed element, only visible on mobile) --}}
            <aside
                x-show="mobileOpen"
                x-cloak
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="-translate-x-full"
                x-transition:enter-end="translate-x-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="translate-x-0"
                x-transition:leave-end="-translate-x-full"
                class="fixed inset-y-0 left-0 z-30 w-[250px] lg:hidden"
            >
                @include('layouts.navigation')
            </aside>

            {{-- ── Main Content Area ────────────────────────────────────── --}}
            <div class="flex flex-col flex-1 min-w-0 overflow-hidden">

                {{-- Top Bar --}}
                <header class="flex items-center h-14 px-4 bg-white border-b border-gray-200 shrink-0 shadow-sm">

                    {{-- Desktop Toggle (collapse sidebar) --}}
                    <div class="hidden lg:flex items-center">
                        <button
                            @click="sidebarOpen = !sidebarOpen"
                            type="button"
                            class="inline-flex items-center justify-center w-9 h-9 rounded-lg
                                   text-gray-500 hover:text-gray-900 hover:bg-gray-100
                                   focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500
                                   transition duration-150"
                            :aria-expanded="sidebarOpen.toString()"
                            aria-label="Toggle sidebar"
                        >
                            {{-- Menu icon --}}
                            <svg x-show="!sidebarOpen"
                                 class="w-5 h-5" xmlns="http://www.w3.org/2000/svg"
                                 fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                            {{-- Close / collapse icon --}}
                            <svg x-show="sidebarOpen" x-cloak
                                 class="w-5 h-5" xmlns="http://www.w3.org/2000/svg"
                                 fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                            </svg>
                        </button>
                    </div>

                    {{-- Mobile Hamburger --}}
                    <div class="flex items-center lg:hidden">
                        <button
                            @click="mobileOpen = !mobileOpen"
                            type="button"
                            class="inline-flex items-center justify-center w-9 h-9 rounded-lg
                                   text-gray-500 hover:text-gray-900 hover:bg-gray-100
                                   focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500
                                   transition duration-150"
                            aria-label="Open menu"
                        >
                            <svg x-show="!mobileOpen"
                                 class="w-5 h-5" xmlns="http://www.w3.org/2000/svg"
                                 fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                            <svg x-show="mobileOpen" x-cloak
                                 class="w-5 h-5" xmlns="http://www.w3.org/2000/svg"
                                 fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    {{-- App Name / Breadcrumb placeholder --}}
                    <div class="ml-3 flex-1">
                        @isset($header)
                            {{ $header }}
                        @endisset
                    </div>

                </header>

                {{-- Page Content --}}
                <main class="flex-1 overflow-y-auto p-6">
                    {{ $slot }}
                </main>

            </div>

        </div>

        <!-- Toast notification -->
        <x-toast-notification />
        
        @stack('scripts')
    </body>
</html>
