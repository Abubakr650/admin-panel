{{-- تيم معدل بتيلويند - Tailwind Dynamic Theme Switcher --}}
<div x-data="{ open: false }" class="mt-1">
    {{-- Button with a beautified and enlarged color square --}}
    <button @click="open = !open" 
            class="flex items-center w-full gap-3 px-4 py-2 text-[11px] font-bold uppercase tracking-wider text-gray-400 hover:text-theme-from transition-all rounded-lg outline-none group">
        <div class="w-5 h-5 rounded-md bg-gradient-to-br from-theme-from to-theme-to shadow-md shadow-theme-from/20 ring-2 ring-white border border-theme-from/10"></div>
        <span x-show="sidebarOpen" class="flex-1 text-left">Theme Style</span>
        <x-icon name="chevron-down" x-show="sidebarOpen" 
                class="w-3.5 h-3.5 transition-transform duration-200 text-gray-300"
                ::class="open ? 'rotate-180 text-theme-from' : ''" />
    </button>

    <div x-show="open && sidebarOpen" 
         x-collapse
         class="mt-2 px-1 ml-6">
        <div class="grid grid-cols-4 gap-2.5 p-2 bg-white border border-gray-100 rounded-[2rem] shadow-xl shadow-gray-200/50">
            @foreach($allThemes as $key => $theme)
                <form action="{{ route('theme.update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="theme" value="{{ $key }}">
                    <button type="submit" 
                            title="{{ $theme['name'] }}"
                            class="w-full aspect-square rounded-full border-[3px] transition-all duration-300 hover:scale-125
                                   {{ $currentThemeKey === $key ? 'border-theme-from ring-2 ring-theme-from/10 shadow-md' : 'border-white shadow-sm ring-1 ring-gray-100' }}"
                            style="background: linear-gradient(135deg, {{ $theme['from'] }}, {{ $theme['to'] }})">
                    </button>
                </form>
            @endforeach
        </div>
    </div>

    {{-- Mobile Popover for collapsed sidebar --}}
    <div x-show="open && !sidebarOpen" 
         x-cloak
         @click.away="open = false"
         class="fixed left-20 bottom-4 z-50 grid grid-cols-4 gap-4 p-4 bg-white border border-gray-100 rounded-[2.5rem] shadow-2xl min-w-[220px]">
        @foreach($allThemes as $key => $theme)
            <form action="{{ route('theme.update') }}" method="POST">
                @csrf
                <input type="hidden" name="theme" value="{{ $key }}">
                <button type="submit" 
                        title="{{ $theme['name'] }}"
                        class="w-12 h-12 rounded-full border-[3px] transition-all hover:scale-125
                               {{ $currentThemeKey === $key ? 'border-theme-from ring-2 ring-theme-from/10 shadow-md' : 'border-white shadow-sm ring-1 ring-gray-100' }}"
                        style="background: linear-gradient(135deg, {{ $theme['from'] }}, {{ $theme['to'] }})">
                </button>
            </form>
        @endforeach
    </div>
</div>
