@props([
    'editRoute'    => null,    {{-- Edit parameter --}}
    'deleteRoute'  => null,    {{-- Delete parameter --}}
    'restoreRoute' => null,    {{-- Restore parameter --}}
    'archived'     => false,   {{-- Archived parameter --}}
    'itemName'     => 'this record',  {{-- Item Name parameter --}}
])

<td class="px-5 py-3.5">
    <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity duration-150">

        @if($archived && $restoreRoute)
            {{-- Restore --}}
            <form action="{{ $restoreRoute }}" method="POST" class="inline-block">
                @csrf
                @method('PUT')
                <button type="submit"
                        onclick="this.disabled=true; this.form.submit();"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-theme-from/10 text-theme-from font-semibold border border-theme-from/10 hover:bg-theme-from/30 hover:border-theme-from/40 hover:shadow hover:-translate-y-0.5 active:scale-95 transition-all duration-200">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Restore
                </button>
            </form>

        @else
            {{-- Edit --}}
            @if($editRoute)
                <a href="{{ $editRoute }}"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-theme-from/10 text-theme-from font-semibold border border-theme-from/10 hover:bg-theme-from/30 hover:border-theme-from/40 hover:shadow hover:-translate-y-0.5 active:scale-95 transition-all duration-200">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit
                </a>
            @endif

            {{-- Delete / Archive --}}
            @if($deleteRoute)
                <form action="{{ $deleteRoute }}" method="POST" class="inline-block"
                      onsubmit="return confirm('Archive {{ $itemName }}?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-red-50 text-red-600 font-semibold border border-red-50 hover:bg-red-200/50 hover:border-red-200 hover:shadow hover:-translate-y-0.5 active:scale-95 transition-all duration-200">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                        </svg>
                        Archive
                    </button>
                </form>
            @endif

            {{-- Extra actions slot --}}
            {{ $slot ?? '' }}
        @endif

    </div>
</td>
