<x-app-layout>
@php $isArchived = request()->input('archived') === 'true'; @endphp
    <x-slot name="header">
        <div class="flex items-center justify-between w-full gap-4">
            {{-- Left: Title + Search --}}
            <div class="flex items-center gap-3 flex-1 min-w-0">
                {{-- Page Title --}}
                <div class="shrink-0">
                    <p class="text-xs text-gray-400 uppercase tracking-widest font-semibold">Pharmacy</p>
                    <h1 class="text-lg font-bold text-gray-800 leading-tight">
                        {{ $isArchived ? 'Archived Items' : 'Pharmacy Items' }}
                    </h1>
                </div>

                {{-- Divider --}}
                <div class="h-8 w-px bg-gray-200 shrink-0"></div>

                {{-- Search Bar --}}
                <div class="w-64">
                    <x-search-bar
                        action="{{ route('pharmacy.items.index') }}"
                        placeholder="Search by any field…"
                        :extraParams="array_filter(['archived' => request('archived'), 'form' => request('form'), 'category' => request('category')])"
                    >
                        {{-- Form Filter --}}
                        @if($formOptions->isNotEmpty())
                        <div class="flex items-center gap-2 flex-nowrap">
                            <select name="form" @change="$refs.form.submit()"
                                class="block py-2 pl-3 pr-8 border border-gray-200 rounded-xl text-sm focus:ring-theme-from focus:border-theme-from font-medium bg-white flex-shrink-0 min-w-[120px]"
                                style="background-image: url('data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' fill=\'none\' viewBox=\'0 0 24 24\' stroke=\'%236b7280\'%3E%3Cpath stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M19 9l-7 7-7-7\'/%3E%3C/svg%3E'); background-position: right 8px center; background-size: 14px; -webkit-appearance: none; appearance: none;">
                                <option value="">All Forms</option>
                                @foreach($formOptions as $opt)
                                    <option value="{{ $opt }}" {{ request('form') === $opt ? 'selected' : '' }}>{{ ucfirst($opt) }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif

                        {{-- Category Filter --}}
                        @if($categoryOptions->isNotEmpty())
                        <div class="flex items-center gap-2 flex-nowrap">
                            <select name="category" @change="$refs.form.submit()"
                                class="block py-2 pl-3 pr-8 border border-gray-200 rounded-xl text-sm focus:ring-theme-from focus:border-theme-from font-medium bg-white flex-shrink-0 min-w-[130px]"
                                style="background-image: url('data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' fill=\'none\' viewBox=\'0 0 24 24\' stroke=\'%236b7280\'%3E%3Cpath stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M19 9l-7 7-7-7\'/%3E%3C/svg%3E'); background-position: right 8px center; background-size: 14px; -webkit-appearance: none; appearance: none;">
                                <option value="">All Categories</option>
                                @foreach($categoryOptions as $opt)
                                    <option value="{{ $opt }}" {{ request('category') === $opt ? 'selected' : '' }}>{{ ucfirst($opt) }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                    </x-search-bar>
                </div>
            </div>

            {{-- Right: Action Buttons --}}
            <div class="flex items-center gap-3 shrink-0">
                {{-- Scan QR Button --}}
                @unless($isArchived)
                    <button x-on:click="$dispatch('open-modal', 'scan-qr')"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 rounded-xl text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-all duration-200 shadow-sm">
                        <x-icon name="qr-code" class="w-4 h-4 text-theme-from" />
                        Scan Mode
                    </button>
                @endunless

                @unless($isArchived)
                    <x-button-link href="{{ route('pharmacy.items.create') }}">
                        <x-icon name="plus" class="w-4 h-4" />
                        New Item
                    </x-button-link>
                @endunless

                <x-button-archive-view
                    :archived="$isArchived"
                    :indexRoute="route('pharmacy.items.index')"
                    :archiveRoute="route('pharmacy.items.index', ['archived' => 'true'])"
                    :archivedCount="$archivedCount ?? 0"
                />
            </div>
        </div>
    </x-slot>

    {{-- Scan QR Modal --}}
    <x-scan-qr-modal 
        name="scan-qr" 
        mode="search" 
        :action="route('pharmacy.items.index')"
        :extraParams="array_filter([
            'archived' => request('archived'),
            'form' => request('form'),
            'category' => request('category')
        ])"
    />

    {{-- Page Body --}}
    <div class="space-y-4">

        {{-- Toast --}}
        {{-- Stats Row --}}
        @if(!$isArchived)
        <div class="grid grid-cols-2 sm:grid-cols-2 gap-4">
            {{-- Total Items --}}
            <div class="relative overflow-hidden rounded-2xl bg-white border border-gray-100 shadow-sm p-4 flex items-center gap-4">
                <div class="flex-shrink-0 flex items-center justify-center w-11 h-11 rounded-xl bg-theme-from/10">
                    <x-icon name="clipboard" class="w-5 h-5 text-theme-from" />
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total Items</p>
                    <p class="text-2xl font-extrabold text-gray-800">{{ $items->total() ?? '—' }}</p>
                </div>
                <div class="absolute -right-3 -bottom-3 w-16 h-16 rounded-full bg-theme-from/10 opacity-60"></div>
            </div>
        </div>
        @endif

        {{-- Table Card --}}
        <div class="rounded-2xl bg-white border border-gray-100 shadow-sm overflow-hidden">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-gray-50/70">
                        <th class="rounded-l-lg px-6 py-3 text-left text-md leading-4 font-semibold text-gray-500 uppercase tracking-wider">#</th>
                        <th class="px-6 py-3 text-left text-md leading-4 font-semibold text-gray-500 uppercase tracking-wider">Commercial Name</th>
                        <th class="px-6 py-3 text-left text-md leading-4 font-semibold text-gray-500 uppercase tracking-wider">Scientific Name</th>
                        <th class="px-6 py-3 text-left text-md leading-4 font-semibold text-gray-500 uppercase tracking-wider">Form</th>
                        <th class="px-6 py-3 text-left text-md leading-4 font-semibold text-gray-500 uppercase tracking-wider">Company</th>
                        <th class="px-6 py-3 text-left text-md leading-4 font-semibold text-gray-500 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-3 text-left text-md leading-4 font-semibold text-gray-500 uppercase tracking-wider text-center">QR</th>
                        <th class="px-6 py-3 text-left text-md leading-4 font-semibold text-gray-500 uppercase tracking-wider">Location</th>
                        @if($isArchived)
                            <th class="px-6 py-3 text-left text-md leading-4 font-semibold text-gray-500 uppercase tracking-wider">Archive Date</th>
                        @endif
                        <th class="rounded-l-lg px-6 py-3 bg-gray-50 text-right text-md leading-4 font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($items as $item)
                        <tr class="group hover:bg-theme-from/10 transition-colors duration-150" x-data="{ showQr: false }">
                            {{-- Row number --}}
                            <td class="px-5 py-3.5 font-semibold text-gray-300">{{ $loop->iteration }}</td>

                            {{-- Commercial Name --}}
                            <td class="px-5 py-3.5">
                                @if($isArchived)
                                    <p class="font-semibold text-gray-700">{{ $item->commercial_name }}</p>
                                @else
                                    <a href="{{ route('pharmacy.items.show', $item->id) }}"
                                       class="font-semibold text-gray-800 hover:text-theme-from transition-colors duration-150">
                                        {{ $item->commercial_name }}
                                    </a>
                                @endif
                            </td>

                            {{-- Scientific Name --}}
                            <td class="px-5 py-3.5 text-gray-600">
                                {{ $item->scientific_name ?? '—' }}
                            </td>

                            {{-- Form --}}
                            <td class="px-5 py-3.5">
                                @if($item->form)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-sky-50 text-sky-600 border border-sky-100">
                                        {{ ucfirst($item->form) }}
                                    </span>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>

                            {{-- Company --}}
                            <td class="px-5 py-3.5 text-gray-600">
                                {{ $item->company_name ?? '—' }}
                            </td>

                            {{-- Category --}}
                            <td class="px-5 py-3.5">
                                @if($item->category)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-theme-from/10 text-theme-from border border-theme-from/20">
                                        {{ $item->category }}
                                    </span>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>

                            {{-- QR Code View --}}
                            <td class="px-5 py-3.5 text-center">
                                @if($item->qr_code)
                                    <button x-on:click="$dispatch('open-modal', 'qr-{{ $item->id }}')" 
                                        class="p-1.5 rounded-lg bg-gray-50 text-gray-400 hover:text-theme-from hover:bg-theme-from/10 transition-all duration-200">
                                        <x-icon name="qr-code" class="w-5 h-5" />
                                    </button>

                                    {{-- Row-specific QR Modal --}}
                                    <x-modal name="qr-{{ $item->id }}" focusable>
                                        <div class="p-8 text-center text-md">
                                            <div class="mb-4 flex justify-center">
                                                <div class="p-4 bg-white rounded-2xl border-2 border-dashed border-gray-100 shadow-sm">
                                                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode($item->qr_code) }}" 
                                                         alt="QR Code" class="w-48 h-48">
                                                </div>
                                            </div>
                                            <h3 class="text-xl font-bold text-gray-800">{{ $item->commercial_name }}</h3>
                                            <p class="text-sm font-mono text-gray-500 mt-1">{{ $item->qr_code }}</p>
                                            
                                            <div class="mt-6">
                                                <button x-on:click="$dispatch('close-modal', 'qr-{{ $item->id }}')" 
                                                    class="px-6 py-2 bg-gray-100 text-gray-600 rounded-xl font-bold hover:bg-gray-200 transition-colors">
                                                    Close
                                                </button>
                                            </div>
                                        </div>
                                    </x-modal>
                                @else
                                    <span class="text-gray-300">—</span>
                                @endif
                            </td>

                            {{-- Location --}}
                            <td class="px-5 py-3.5">
                                @if($item->location_in_pharmacy)
                                    <div class="flex items-center gap-1.5 text-gray-600">
                                        <x-icon name="location" class="w-4 h-4 text-gray-300 flex-shrink-0" />
                                        {{ $item->location_in_pharmacy }}
                                    </div>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>

                            {{-- Archive Date --}}
                            @if($isArchived)
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-1.5 text-gray-500">
                                        <x-icon name="calendar" class="w-3.5 h-3.5 text-amber-500" />
                                        {{ $item->deleted_at->format('M d, Y') }}
                                    </div>
                                </td>
                            @endif

                            {{-- Actions --}}
                            <x-table-actions
                                :archived="$isArchived"
                                :editRoute="route('pharmacy.items.edit', $item->id)"
                                :deleteRoute="route('pharmacy.items.destroy', $item->id)"
                                :restoreRoute="route('pharmacy.items.restore', $item->id)"
                                :itemName="$item->commercial_name"
                            />
                        </tr>
                    @empty
                        <x-message-empty colspan="{{ $isArchived ? 10 : 9 }}">
                            {{ $isArchived ? 'No archived items found' : 'No pharmacy items found' }}
                        </x-message-empty>
                    @endforelse
                </tbody>
            </table>

            {{-- Pagination --}}
            @if($items->hasPages())
                <div class="px-5 py-3 border-t border-gray-100 bg-gray-50/50">
                    {{ $items->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
