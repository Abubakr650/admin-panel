<nav
    class="flex flex-col h-full bg-white border-r border-gray-200 overflow-x-hidden"
    :class="(!isMobile() && !sidebarOpen) ? 'w-[64px]' : 'w-[250px]'"
    style="transition: width 0.3s ease"
>

    {{-- ── Logo ──────────────────────────────────────────────────── --}}
    <div class="flex items-center px-4 border-b border-gray-200 py-4 h-14 shrink-0 overflow-hidden">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2 min-w-0">
            <x-application-logo class="w-7 h-7 shrink-0" />
            <span
                class="text-base font-bold text-gray-800 whitespace-nowrap overflow-hidden transition-all duration-300"
                :class="(!isMobile() && !sidebarOpen) ? 'opacity-0 w-0' : 'opacity-100'"
            >
                Ibtisama System
            </span>
        </a>
    </div>

    {{-- ── Navigation Links ────────────────────────────────────────── --}}
    <ul class="flex flex-col px-2 py-3 space-y-0.5 overflow-y-auto overflow-x-hidden flex-1">

        {{-- Dashboard --}}
        <li>
            <a
                href="{{ route('dashboard') }}"
                title="Dashboard"
                class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition duration-150 ease-in-out
                       {{ request()->routeIs('dashboard')
                           ? 'bg-theme-from/10 text-theme-from'
                           : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}"
            >
                <svg class="w-5 h-5 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span class="whitespace-nowrap overflow-hidden transition-all duration-300"
                      :class="(!isMobile() && !sidebarOpen) ? 'opacity-0 w-0' : 'opacity-100'">
                    Dashboard
                </span>
            </a>
        </li>

        {{-- Divider --}}
        <li class="py-1"><hr class="border-gray-100"></li>

        {{-- ─── Admin ────────────────────────────────────────────── --}}
        <x-nav-section
            title="Admin"
            :active="request()->routeIs('users.*')"
        >
            <x-slot:icon>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </x-slot:icon>

            <x-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')">
                <svg class="w-3.5 h-3.5 mr-2 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Users
            </x-nav-link>

            {{-- تيم معدل بتيلويند - Tailwind Dynamic Theme Switcher --}}
            <div class="px-3 py-1 mt-1">
                <x-theme-switcher />
            </div>
        </x-nav-section>

        {{-- Divider --}}
        <li class="py-1"><hr class="border-gray-100"></li>

        {{-- ─── Clinic ────────────────────────────────────────────── --}}
        <x-nav-section
            title="Clinic"
            :active="request()->routeIs('patients.*','doctors.*','appointments.*','treatments.*')"
        >
            <x-slot:icon>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0H5m14 0h2M5 21H3M9 7h1m-1 4h1m4-4h1m-1 4h1M9 17h6"/>
                </svg>
            </x-slot:icon>

            <x-nav-link :href="route('patients.index')" :active="request()->routeIs('patients.*')">
                <svg class="w-3.5 h-3.5 mr-2 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Patients
            </x-nav-link>

            <x-nav-link :href="route('doctors.index')" :active="request()->routeIs('doctors.*')">
                <svg class="w-3.5 h-3.5 mr-2 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                Doctors
            </x-nav-link>

            <x-nav-link :href="route('appointments.index')" :active="request()->routeIs('appointments.*')">
                <svg class="w-3.5 h-3.5 mr-2 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Appointments
            </x-nav-link>

            <!-- treatments مخفيه لانه التعامل معه خطير هي فقط همزة وصل  -->
             <!-- الغي الاخفاء في حالة الرغبة بالاختبار -->
            {{-- 
            <x-nav-link :href="route('treatments.index')" :active="request()->routeIs('treatments.*')">
                <svg class="w-3.5 h-3.5 mr-2 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342"/>
                </svg>
                Treatments
            </x-nav-link>
            --}}
        </x-nav-section>

        {{-- ─── Billing ───────────────────────────────────────────── --}}
        <x-nav-section
            title="Billing"
            :active="request()->routeIs('invoices.*','payments.*','services.*','currencies.*')"
        >
            <x-slot:icon>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </x-slot:icon>

            <x-nav-link :href="route('invoices.index')" :active="request()->routeIs('invoices.*')">
                <svg class="w-3.5 h-3.5 mr-2 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Invoices
            </x-nav-link>

            <x-nav-link :href="route('payments.index')" :active="request()->routeIs('payments.*')">
                <svg class="w-3.5 h-3.5 mr-2 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
                Payments
            </x-nav-link>

            <x-nav-link :href="route('services.index')" :active="request()->routeIs('services.*')">
                <svg class="w-3.5 h-3.5 mr-2 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                Services
            </x-nav-link>

            <x-nav-link :href="route('currencies.index')" :active="request()->routeIs('currencies.*')">
                <svg class="w-3.5 h-3.5 mr-2 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                Currencies
            </x-nav-link>
        </x-nav-section>

        {{-- ─── Pharmacy ──────────────────────────────────────────── --}}
        <x-nav-section
            title="Pharmacy"
            :active="request()->routeIs('pharmacy.*')"
        >
            <x-slot:icon>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                </svg>
            </x-slot:icon>

            <x-nav-link :href="route('pharmacy.items.index')" :active="request()->routeIs('pharmacy.items.*')">
                <svg class="w-3.5 h-3.5 mr-2 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                Items
            </x-nav-link>

            <x-nav-link :href="route('pharmacy.batches.index')" :active="request()->routeIs('pharmacy.batches.*')">
                <svg class="w-3.5 h-3.5 mr-2 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
                Batches
            </x-nav-link>

            <x-nav-link :href="route('pharmacy.suppliers.index')" :active="request()->routeIs('pharmacy.suppliers.*')">
                <svg class="w-3.5 h-3.5 mr-2 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                </svg>
                Suppliers
            </x-nav-link>

            <x-nav-link :href="route('pharmacy.dispense')" :active="request()->routeIs('pharmacy.dispense*')">
                <svg class="w-3.5 h-3.5 mr-2 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>
                </svg>
                Dispense / Sales
            </x-nav-link>

            <x-nav-link :href="route('pharmacy.warehouse.index')" :active="request()->routeIs('pharmacy.warehouse.*')">
                <svg class="w-3.5 h-3.5 mr-2 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                </svg>
                Warehouse
            </x-nav-link>
        </x-nav-section>

        {{-- ─── Radiology ─────────────────────────────────────────── --}}
        <x-nav-section
            title="Radiology"
            :active="request()->routeIs('radiology.*')"
        >
            <x-slot:icon>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18"/>
                </svg>
            </x-slot:icon>

            <x-nav-link :href="route('radiology.scans.index')" :active="request()->routeIs('radiology.scans.*')">
                <svg class="w-3.5 h-3.5 mr-2 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Scans
            </x-nav-link>
        </x-nav-section>

        {{-- ─── Orthodontics ───────────────────────────────────────── --}}
        <x-nav-section
            title="Orthodontics"
            :active="request()->routeIs('orthodontics.*')"
        >
            <x-slot:icon>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </x-slot:icon>

            <x-nav-link :href="route('orthodontics.cases.index')" :active="request()->routeIs('orthodontics.cases.*')">
                <svg class="w-3.5 h-3.5 mr-2 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2M9 14l2 2 4-4"/>
                </svg>
                Cases
            </x-nav-link>

            <x-nav-link :href="route('orthodontics.sessions.index')" :active="request()->routeIs('orthodontics.sessions.*')">
                <svg class="w-3.5 h-3.5 mr-2 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Sessions
            </x-nav-link>
        </x-nav-section>

        {{-- Divider --}}
        <li class="py-1 mt-auto"><hr class="border-gray-100"></li>

        {{-- Logout --}}
        <li>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button
                    type="submit"
                    title="Logout"
                    class="flex items-center gap-3 px-3 py-2 w-full rounded-lg text-sm font-medium
                           text-red-500 hover:text-red-700 hover:bg-red-50
                           focus:outline-none transition duration-150 ease-in-out"
                >
                    <svg class="w-5 h-5 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    <span class="whitespace-nowrap overflow-hidden transition-all duration-300"
                          :class="(!isMobile() && !sidebarOpen) ? 'opacity-0 w-0' : 'opacity-100'">
                        Logout
                    </span>
                </button>
            </form>
        </li>

    </ul>

</nav>