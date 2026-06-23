@props([
    'href'  => '#',
    'label' => 'Back',
])

<a href="{{ $href }}"
   class="group inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full
          bg-white border border-gray-200 shadow-sm
          text-sm font-semibold text-gray-600
          hover:bg-gray-50 hover:text-theme-from hover:border-teal-300 hover:shadow-md
          active:scale-95 transition-all duration-200">
    <span class="flex items-center justify-center w-5 h-5 rounded-full bg-gray-100 group-hover:bg-theme-from/10 transition-colors duration-200">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 text-gray-500 group-hover:text-theme-from transition-colors duration-200"
             fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"
             stroke-linecap="round" stroke-linejoin="round">
            <path d="M19 12H5"/>
            <path d="M12 19l-7-7 7-7"/>
        </svg>
    </span>
    <span>{{ $label }}</span>
</a>