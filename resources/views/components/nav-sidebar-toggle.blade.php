{{--
    Sidebar Toggle Button Component
    Usage: <x-nav-sidebar-toggle />

    Props (read from $attributes or parent x-data):
    - none required — it uses the parent Alpine.js context (sidebarOpen)

    Place this inside any element that has x-data="{ sidebarOpen: ... }" in a parent.
--}}

<button
    @click="sidebarOpen = !sidebarOpen"
    type="button"
    class="inline-flex items-center justify-center w-9 h-9 rounded-lg
           text-gray-500 hover:text-gray-900 hover:bg-gray-100
           focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500
           transition duration-150 ease-in-out"
    :aria-expanded="sidebarOpen.toString()"
    aria-label="Toggle sidebar"
>
    {{-- Hamburger Icon (visible when sidebar is CLOSED) --}}
    <svg
        x-show="!sidebarOpen"
        class="w-5 h-5"
        xmlns="http://www.w3.org/2000/svg" fill="none"
        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"
    >
        <path stroke-linecap="round" stroke-linejoin="round"
              d="M4 6h16M4 12h16M4 18h16"/>
    </svg>

    {{-- Close / X Icon (visible when sidebar is OPEN) --}}
    <svg
        x-show="sidebarOpen"
        x-cloak
        class="w-5 h-5"
        xmlns="http://www.w3.org/2000/svg" fill="none"
        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"
    >
        <path stroke-linecap="round" stroke-linejoin="round"
              d="M6 18L18 6M6 6l12 12"/>
    </svg>
</button>
