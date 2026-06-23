@props(['action', 'placeholder' => 'Search', 'extraParams' => []])

<form method="GET" action="{{ $action }}" class="w-full relative"
    x-ref="form"
    x-data="{
        searchTerm: '{{ request()->input('search') }}',
        isLoading: false,
        timer: null,
        handleSearch() {
            clearTimeout(this.timer);
            this.timer = setTimeout(() => {
                this.submitForm();
            }, 2000);
        },
        submitForm() {
            clearTimeout(this.timer);
            this.isLoading = true;
            this.$refs.form.submit();
        }
    }">

    {{-- Preserved hidden fields --}}
    @if(request()->input('archived') == 'true')
        <input type="hidden" name="archived" value="true">
    @endif
    @foreach($extraParams as $key => $val)
        <input type="hidden" name="{{ $key }}" value="{{ $val }}">
    @endforeach

    <div class="flex flex-nowrap items-center gap-3">
        <div class="relative flex-1 min-w-[200px]">
            <!-- Spinner Indicator -->
            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none"
                x-show="isLoading">
                <svg class="animate-spin h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>

            <!-- Permanent Search Icon -->
            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none"
                x-show="!isLoading">
                <svg class="h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>

            <input
                type="text"
                name="search"
                placeholder="{{ $placeholder }}"
                x-model="searchTerm"
                class="px-6 py-2 flex border rounded-lg w-full pl-8 focus:outline-none focus:ring-2 focus:ring-theme-from/40 focus:border-theme-from transition-all duration-200"
                x-on:input="handleSearch()"
                x-on:keydown.enter.prevent="submitForm()"
                :class="{ 'opacity-50': isLoading }"
                :disabled="isLoading"
                x-init="$watch('isLoading', value => {
                    if (!value) {
                        $nextTick(() => $el.focus());
                    }
                })"
                x-effect="if (!isLoading && searchTerm) {
                    $nextTick(() => $el.focus())
                }"
            >
        </div>

        {{ $slot }}

        @if(request()->filled('search'))
            @php
                $clearUrl = $action . (str_contains($action, '?') ? '&' : '?') . http_build_query($extraParams);
            @endphp
            <a href="{{ $clearUrl }}" class="text-sm text-gray-500 hover:text-theme-from font-medium">Clear</a>
        @endif
    </div>
</form>
