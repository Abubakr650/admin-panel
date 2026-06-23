{{-- تيم معدل بتيلويند - Tailwind Dynamic Theme Button --}}
@props([
    'href'    => '#',
    'variant' => 'primary',  {{-- primary | outline --}}
    'color'   => 'theme',    {{-- theme | teal | indigo | red | blue | gray --}}
])

@php
$primaryColors = [
    'theme'  => 'from-theme-from to-theme-to hover:brightness-110 shadow-theme-shadow/40 hover:shadow-theme-shadow/60',
    'teal'   => 'from-theme-from to-theme-to hover:brightness-110 shadow-theme-shadow/40 hover:shadow-theme-shadow/60',
    'indigo' => 'from-indigo-500 to-indigo-700 hover:brightness-110 shadow-indigo-200/60 hover:shadow-indigo-300/60',
    'red'    => 'from-red-500 to-red-700 hover:brightness-110 shadow-red-200/60 hover:shadow-red-300/60',
    'blue'   => 'from-blue-500 to-blue-700 hover:brightness-110 shadow-blue-200/60 hover:shadow-blue-300/60',
    'amber'  => 'from-amber-400 to-amber-600 hover:brightness-110 shadow-amber-200/60 hover:shadow-amber-300/60',
    'gray'   => 'from-gray-500 to-gray-700 hover:brightness-110 shadow-gray-200/60 hover:shadow-gray-300/60',
];

$outlineColors = [
    'theme'  => 'border-theme-from text-theme-from hover:bg-theme-from/10 hover:border-theme-to',
    'teal'   => 'border-theme-from text-theme-from hover:bg-theme-from/10 hover:border-theme-to',
    'indigo' => 'border-indigo-200 text-indigo-600 hover:bg-indigo-50 hover:border-indigo-300',
    'red'    => 'border-red-200 text-red-600 hover:bg-red-50 hover:border-red-300',
    'blue'   => 'border-blue-200 text-blue-600 hover:bg-blue-50 hover:border-blue-300',
    'amber'  => 'border-amber-200 text-amber-600 hover:bg-amber-50 hover:border-amber-300',
    'gray'   => 'border-gray-200 text-gray-600 hover:bg-gray-50 hover:border-gray-300',
];

$isPrimary = $variant === 'primary';
$colorClass = $isPrimary
    ? ($primaryColors[$color] ?? $primaryColors['theme'])
    : ($outlineColors[$color] ?? $outlineColors['gray']);
@endphp

<a href="{{ $href }}"
   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl font-semibold transition-all duration-200
          {{ $isPrimary
              ? 'bg-gradient-to-r text-white shadow-md hover:scale-[1.02] active:scale-[0.98] ' . $colorClass
              : 'border bg-white shadow-sm ' . $colorClass }}"
   {{ $attributes }}>
    <span class="relative flex items-center gap-2">
        {{ $slot }}
    </span>
</a>