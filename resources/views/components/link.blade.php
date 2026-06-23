<a  
    href="{{ $href }}" 
    target="{{ $target ?? '_self' }}"
    rel="{{ $rel ?? '_blank' }}"
    class="inline-flex items-center gap-1 text-blue-500 hover:text-blue-700 transition-colors duration-200 font-medium break-all hover:underline"
>
    {{ $slot }}
    @if(($target ?? '_self') === '_blank')
        <svg 
            xmlns="http://www.w3.org/2000/svg" 
            class="h-4 w-4"
            viewBox="0 0 24 24" 
            fill="none" 
            stroke="currentColor" 
            stroke-width="2" 
            stroke-linecap="round" 
            stroke-linejoin="round"
        >
            <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
            <polyline points="15 3 21 3 21 9"></polyline>
            <line x1="10" y1="14" x2="21" y2="3"></line>
        </svg>
    @endif
</a>