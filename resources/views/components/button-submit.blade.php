{{-- تيم معدل بتيلويند - Tailwind Dynamic Theme Submit Button --}}
<button 
    x-data="{ loading: false }"
    x-init="$el.form.addEventListener('submit', () => { loading = true })"
    {{ $attributes->merge([
        'type' => 'submit', 
        'class' => 'inline-flex items-center justify-center px-6 py-2.5 border border-transparent
                   text-sm font-semibold rounded-xl text-white
                   bg-gradient-to-r from-theme-from to-theme-to
                   hover:brightness-110
                   focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-theme-from
                   transform transition-all duration-200 ease-in-out
                   hover:scale-[1.02] active:scale-[0.98]
                   shadow-md shadow-theme-shadow/40 hover:shadow-lg hover:shadow-theme-shadow/60
                   disabled:opacity-60 disabled:cursor-not-allowed
                   min-w-[140px] gap-2'
    ]) }}
    x-bind:disabled="loading"
>
    {{-- Loading Spinner --}}
    <svg x-show="loading" x-cloak class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
    </svg>

    {{-- Button Content --}}
    <span x-show="!loading" class="flex items-center gap-1.5">
        {{ $slot }}
    </span>
    <span x-show="loading" x-cloak class="text-white/90">Please wait...</span>
</button>