@props([
    'href'  => '#',
    'icon'  => null,
    'label' => null,
    'title' => '',
    'subtitle' => null,
    'color' => 'theme-from', {{-- theme-from | theme-to | emerald | amber | etc --}}
    'size'  => 'md',         {{-- sm | md --}}
])

@php
    // Mapping sizes to Tailwind classes
    $sizes = [
        'sm' => [
            'p' => 'p-3.5',
            'i_box' => 'w-9 h-9 rounded-lg',
            'i_size' => 'w-5 h-5',
            't_size' => 'text-xs',
            'a_size' => 'w-3.5 h-3.5',
        ],
        'md' => [
            'p' => 'p-4',
            'i_box' => 'w-12 h-12 rounded-xl',
            'i_size' => 'w-6 h-6',
            't_size' => 'text-sm',
            'a_size' => 'w-4 h-4',
        ],
    ];
    $s = $sizes[$size] ?? $sizes['md'];

    // If color matches a theme variable, use it directly. Otherwise, assume Tailwind color.
    $isTheme = str_starts_with($color, 'theme-');
    
    // Explicit class mapping to ensure Tailwind JIT picks up the classes
    $hovBorder = [
        'theme-from' => 'hover:border-theme-from',
        'theme-to'   => 'hover:border-theme-to',
    ][$color] ?? ($isTheme ? "hover:border-$color" : "hover:border-$color-500");

    $iconBg = [
        'theme-from' => 'bg-theme-from/10',
        'theme-to'   => 'bg-theme-to/10',
    ][$color] ?? ($isTheme ? "bg-$color/10" : "bg-$color-100");

    $iconHovBg = [
        'theme-from' => 'group-hover:bg-theme-from',
        'theme-to'   => 'group-hover:bg-theme-to',
    ][$color] ?? ($isTheme ? "group-hover:bg-$color" : "group-hover:bg-$color-500");

    $iconTxt = [
        'theme-from' => 'text-theme-from',
        'theme-to'   => 'text-theme-to',
    ][$color] ?? ($isTheme ? "text-$color" : "text-$color-600");

    $arrowTxt = [
        'theme-from' => 'text-theme-from',
        'theme-to'   => 'text-theme-to',
    ][$color] ?? ($isTheme ? "text-$color" : "text-$color-500");
@endphp

<a href="{{ $href }}"
   class="group block {{ $s['p'] }} rounded-2xl bg-gray-50 border border-gray-100 {{ $hovBorder }} hover:bg-white transition-all duration-200"
   {{ $attributes }}>
    <div class="flex items-center gap-4">
        @if($icon)
            <div class="{{ $s['i_box'] }} {{ $iconBg }} flex items-center justify-center {{ $iconTxt }} {{ $iconHovBg }} group-hover:text-white transition-colors">
                <x-icon name="{{ $icon }}" class="{{ $s['i_size'] }}" />
            </div>
        @endif
        
        <div class="flex-1 min-w-0 text-left">
            @if($label)
                <p class="text-[10px] font-bold opacity-60 uppercase tracking-wider mb-0.5">{{ $label }}</p>
            @endif
            <p class="{{ $s['t_size'] }} font-bold text-gray-800 truncate">{{ $title }}</p>
            @if($subtitle)
                <p class="text-[10px] text-gray-500 mt-0.5 truncate">{{ $subtitle }}</p>
            @endif
            @if($slot->isNotEmpty())
                <div class="mt-1">
                    {{ $slot }}
                </div>
            @endif
        </div>

        <div class="ml-auto opacity-0 group-hover:opacity-100 transform translate-x-2 group-hover:translate-x-0 transition-all">
            <x-icon name="arrow-right" class="{{ $s['a_size'] }} {{ $arrowTxt }}" />
        </div>
    </div>
</a>
