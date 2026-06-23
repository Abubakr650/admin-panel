@props([
    'archived'      => false,
    'indexRoute'    => '#',
    'archiveRoute'  => '#',
    'archivedCount' => 0,
])

@if($archived)
    {{-- Back to active --}}
    <a href="{{ $indexRoute }}"
       class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-gray-200 bg-white font-semibold text-gray-600 hover:bg-gray-50 hover:border-gray-300 shadow-sm transition-all duration-200"
       {{ $attributes }}>
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/>
        </svg>
        Back to active
    </a>
@else
    {{-- View archive --}}
    <a href="{{ $archiveRoute }}"
       class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-gray-200 bg-white font-semibold text-gray-500 hover:bg-gray-50 hover:border-gray-300 shadow-sm transition-all duration-200"
       {{ $attributes }}>
        <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
        </svg>
        Archive
        @if($archivedCount > 0)
            <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-gray-100 text-gray-600 text-xs font-bold">
                {{ $archivedCount }}
            </span>
        @endif
    </a>
@endif
