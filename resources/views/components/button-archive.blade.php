<button 
    this.form.submit();
    {{ $attributes->merge([
        'type' => 'submit', 
        'class' => 'inline-flex items-center justify-center px-6 py-2 border border-transparent
                   text-base font-medium rounded-lg text-white
                   bg-gradient-to-r from-red-500 to-red-700
                   hover:from-red-600 hover:to-red-800
                   focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-400
                   transform transition-all duration-200 ease-in-out
                   hover:scale-[1.02] active:scale-[0.98]
                   shadow-md hover:shadow-lg
                   disabled:opacity-60 disabled:cursor-not-allowed
                   min-w-[120px]'
    ]) }}
>
    <span class="relative flex items-center gap-2">
        {{ $slot }}
    </span>
</button>

{{-- <button type="submit"  this.form.submit(); " class="inline-flex items-center px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-700 focus:outline-none focus:shadow-outline">Archive</button> --}}