 <button @click="{{ $toggleVar }} = !{{ $toggleVar }}" 
        class="inline-flex items-center text-theme-from hover:text-theme-from text-base font-medium">
    <span x-text="{{ $toggleVar }} ? 'Show Less' : 'View All'"></span>
        <!-- Lower Arrow -->
        <svg x-show="!{{ $toggleVar }}" 
             class="ml-2 w-5 h-5"
             fill="none" 
             stroke="currentColor" 
             viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                  d="M19 9l-7 7-7-7" />
        </svg>

        <!-- Upper Arrow -->
        <svg x-show="{{ $toggleVar }}" 
             class="ml-2 w-5 h-5"
             fill="none" 
             stroke="currentColor" 
             viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                  d="M5 15l7-7 7 7" />
        </svg>
</button>