@props(['name', 'class' => 'w-4 h-4', 'filled' => false])

@if($filled)
<svg {{ $attributes->merge(['class' => $class]) }} fill="currentColor" viewBox="0 0 24 24">
    @switch($name)

        {{-- ─── Gender Icons ────────────────────────────────────────── --}}
        @case('gender-male')
            <path d="M9 9a4 4 0 104 4A4 4 0 009 9zm-4 4a8 8 0 1111.3 7.3l3.4 3.3-1.4 1.4-3.3-3.4A8 8 0 015 13z"/>
        @break

        @case('gender-female')
            <path d="M12 2a7 7 0 107 7 7 7 0 00-7-7zm0 12a5 5 0 115-5 5 5 0 01-5 5zm-1 2h2v2h2v2h-2v2h-2v-2H9v-2h2z"/>
        @break

    @endswitch
</svg>
@else
<svg {{ $attributes->merge(['class' => $class]) }}
    fill="none" stroke="currentColor" viewBox="0 0 24 24"
    stroke-linecap="round" stroke-linejoin="round">
    @switch($name)

        {{-- ─── User Management ────────────────────────────────────────── --}}
        @case('user')
            <path stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
        @break

        @case('user-add')
            <path stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
        @break

        @case('user-off')
            <path stroke-width="2" d="M13 14H7a6 6 0 00-6 6M17 8l4 4m0-4l-4 4M13 5a4 4 0 11-8 0 4 4 0 018 0z" />
        @break

        @case('users')
            <path stroke-width="1.8" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
        @break

        @case('gender')
            <path stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
        @break

        {{-- ─── General UI ────────────────────────────────────────────── --}}
        @case('calendar')
            <path stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
        @break

        @case('phone')
            <path stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
        @break

        @case('location')
            <path stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
            <path stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
        @break

        @case('mail')
            <path stroke-width="1.8" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
        @break

        @case('plus')
            <path stroke-width="2.5" d="M12 4v16m8-8H4"/>
        @break

        @case('x')
            <path stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        @break

        @case('chevron-down')
            <path stroke-width="2" d="M19 9l-7 7-7-7" />
        @break

        @case('clock')
            <path stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
        @break

        @case('search')
            <path stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        @break

        @case('edit')
            <path stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
        @break

        @case('document')
            <path stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5l5 5v11a2 2 0 01-2 2z" />
        @break

        @case('filter')
            <path stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
        @break

        {{-- ─── Medical & Clinic ──────────────────────────────────────── --}}
        @case('stethoscope')
            <path stroke-width="1.8" d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v11a6 6 0 006 6 6 6 0 006-6v-3" />
            <circle cx="20" cy="14" r="2" stroke-width="1.8"/>
        @break

        @case('academic-cap')
            <path stroke-width="1.8" d="M12 14l9-5-9-5-9 5 9 5z" />
            <path stroke-width="1.8" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
            <path stroke-width="1.8" d="M21 9v6" />
        @break

        @case('clipboard')
            <path stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
        @break

        {{-- ─── Status & Feedback ────────────────────────────────────── --}}
        @case('info')
            <path stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        @break

        @case('check-circle')
            <path stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        @break

        @case('x-circle')
            <path stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
        @break

        @case('exclamation')
            <path stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        @break

        @case('badge-check')
            <path stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
        @break

        @case('arrow-right')
            <path stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
        @break

        @case('arrow-up')
            <path stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12" />
        @break

        @case('arrow-down')
            <path stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6" />
        @break

        @case('home')
            <path stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
        @break

        @case('cog')
            <path stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
            <path stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
        @break

        @case('hospital')
            <path stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0H5m14 0h2M5 21H3M9 7h1m-1 4h1m4-4h1m-1 4h1M9 17h6"/>
        @break

        @case('pill')
            <path stroke-width="1.8" d="M12 4v1m0 11v1m0-11l3 3m-3-3l-3 3M6 10l8 8a6 6 0 11-8.485-8.485L6 10z"/>
        @break

        @case('cube')
            <path stroke-width="1.8" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
        @break

        @case('archive')
            <path stroke-width="1.8" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
        @break

        @case('lightning-bolt')
            <path stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
        @break

        @case('currency-dollar')
            <path stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        @break

        @case('scan')
            <path stroke-width="1.8" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        @break

        @case('eye')
            <path stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            <path stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
        @break

        @case('swatch')
            <path stroke-width="1.8" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
            <path stroke-width="1.8" d="M9 3v4a2 2 0 002 2h4"/>
            <path stroke-width="1.8" d="M8 12h8m-8 4h6"/>
        @break

        @case('logout')
             <path stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
        @break

        {{-- ─── QR Code ────────────────────────────────────────────────── --}}
        @case('qr-code')
            <path stroke-width="1.8" d="M12 4H4v8h8V4zM6 6h4v4H6V6zM20 4h-8v8h8V4zM14 6h4v4h-4V6zM12 20H4v-8h8v8zM6 14h4v4H6v-4zM20 12h-2v2h2v-2zM17 12h-2v2h2v-2zm0 5h2v-2h-2v2zm-3 0h2v-2h-2v2zm0 3h2v-2h-2v2zm3 0h2v-2h-2v2z"/>
        @break

        {{-- ─── Pharmacy / Supplier ───────────────────────────────────────── --}}
        @case('truck')
            <path stroke-width="1.8" d="M1 3h15v13H1V3zm15 4h4l3 3v6h-7V7z"/>
            <circle cx="5.5" cy="18.5" r="1.5" stroke-width="1.8"/>
            <circle cx="18.5" cy="18.5" r="1.5" stroke-width="1.8"/>
        @break

        @case('library')
            <path stroke-width="1.8" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/>
        @break

        @case('cash')
            <path stroke-width="1.8" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
        @break

        @case('credit-card')
            <path stroke-width="1.8" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
        @break

    @endswitch
</svg>
@endif
