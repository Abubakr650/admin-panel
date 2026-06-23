@props([
    'name',
    'value' => null,
    'options' => [],
    'label' => null,
    'icon' => null,
    'placeholder' => 'Select an option',
    'searchPlaceholder' => 'Type to search...',
    'required' => false,
    'quickAddRoute' => null,
    'quickAddTitle' => 'Add New',
])

<div x-data="{
    open: false,
    search: '',
    selectedId: '{{ old($name, $value) }}',
    selectedName: '',
    options: @js($options),
    get filteredOptions() {
        let q = this.search.toLowerCase();
        if (q === '') return this.options;
        return this.options.filter(o => 
            (o.name || '').toLowerCase().includes(q) ||
            (o.subtext || '').toLowerCase().includes(q)
        );
    },
    selectOption(o) {
        this.selectedId = o.id;
        this.selectedName = o.name;
        this.open = false;
        this.search = '';
        window.dispatchEvent(new CustomEvent('searchable-select:change', {
            detail: { name: '{{ $name }}', value: o.id, option: o }
        }));
    },
    init() {
        if (this.selectedId) {
            let o = this.options.find(x => x.id == this.selectedId);
            if (o) this.selectedName = o.name;
        }
    }
}" class="relative">
    @if($label)
        <label class="block text-sm font-semibold text-gray-600 mb-1.5">
            <span class="flex items-center gap-1.5">
                @if($icon) <x-icon :name="$icon" class="w-4 h-4 text-theme-from" /> @endif
                {{ $label }} @if($required) <span class="text-red-400">*</span> @endif
            </span>
        </label>
    @endif

    <div class="flex items-center gap-2">
        <div class="relative flex-1" @click.outside="open = false">
            <input type="hidden" :name="'{{ $name }}'" :value="selectedId">

            {{-- Trigger --}}
            <div 
                @click="open = !open"
                class="w-full px-4 py-2.5 rounded-xl border text-gray-800 text-sm placeholder-gray-400 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-theme-from/40 focus:border-theme-from flex items-center justify-between cursor-pointer bg-no-repeat {{ $errors->has($name) ? 'border-red-400 bg-red-50 ring-1 ring-red-300' : 'border-gray-200 bg-gray-50 hover:border-theme-from' }}"
                :class="open ? 'ring-2 ring-theme-from/40 border-theme-from' : ''"
                style="background-image: url('data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' fill=\'none\' viewBox=\'0 0 24 24\' stroke=\'%236b7280\'%3E%3Cpath stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M19 9l-7 7-7-7\'/%3E%3C/svg%3E'); background-position: right 12px center; background-size: 16px;"
            >
                <span x-text="selectedName || '{{ $placeholder }}'" :class="!selectedName ? 'text-gray-400' : ''"></span>
            </div>

            {{-- Dropdown Panel --}}
            <div 
                x-show="open" 
                x-cloak
                @click.stop
                x-transition:enter="transition ease-out duration-100"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                class="absolute z-50 mt-2 w-full bg-white rounded-xl shadow-2xl border border-gray-100 overflow-hidden"
            >
                <div class="p-2 border-b border-gray-50 bg-gray-50/50">
                    <input 
                        type="text" 
                        x-model="search"
                        placeholder="{{ $searchPlaceholder }}"
                        class="w-full px-3 py-2 text-sm bg-white border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-theme-from/30 focus:border-theme-from"
                    >
                </div>

                <div class="max-h-60 overflow-y-auto">
                    <template x-for="o in filteredOptions" :key="o.id">
                        <div 
                            @click="selectOption(o)"
                            class="px-4 py-2.5 hover:bg-theme-from/5 cursor-pointer flex flex-col gap-0.5 transition-colors border-b border-gray-50 last:border-0"
                            :class="selectedId == o.id ? 'bg-theme-from/5 border-l-4 border-l-theme-from' : ''"
                        >
                            <span class="text-sm font-bold text-gray-800" x-text="o.name"></span>
                            <span x-show="o.subtext" class="text-xs text-gray-400" x-text="o.subtext"></span>
                        </div>
                    </template>
                    <div x-show="filteredOptions.length === 0" class="px-4 py-8 text-center">
                        <x-icon name="search" class="w-8 h-8 text-gray-200 mx-auto mb-2" />
                        <p class="text-gray-400 text-sm">No results found</p>
                    </div>
                </div>
            </div>
        </div>

        @if($quickAddRoute)
            <a href="{{ $quickAddRoute }}" 
               class="shrink-0 w-11 h-11 flex items-center justify-center bg-theme-from/10 text-theme-from rounded-xl hover:bg-theme-from hover:text-white transition-all duration-200 shadow-sm"
               title="{{ $quickAddTitle }}">
                <x-icon name="plus" class="w-5 h-5" />
            </a>
        @endif
    </div>
    <x-input-error :messages="$errors->get($name)" class="mt-1.5" />
</div>
