<x-app-layout>
    @php $isArchived = request()->input('archived') === 'true'; @endphp
    
    <x-slot name="header">
        <div class="flex items-center justify-between w-full gap-4">
            {{-- Left: Title + Search --}}
            <div class="flex items-center gap-3 flex-1 min-w-0">
                <div class="shrink-0">
                    <p class="text-xs text-gray-400 uppercase tracking-widest font-semibold">Pharmacy</p>
                    <h1 class="text-lg font-bold text-gray-800 leading-tight">
                        {{ $isArchived ? 'Archived Warehouse' : 'Warehouse Inventory' }}
                    </h1>
                </div>

                {{-- Divider --}}
                <div class="h-8 w-px bg-gray-200 shrink-0"></div>

                {{-- Search Bar --}}
                <div class="w-64">
                    <x-search-bar
                        action="{{ route('pharmacy.warehouse.index') }}"
                        placeholder="Search for item or company..."
                        :extraParams="array_filter(['archived' => request('archived'), 'category' => request('category')])"
                    >
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
                    <x-button-link href="{{ route('pharmacy.warehouse.create') }}">
                        <x-icon name="plus" class="w-4 h-4" />
                        New Item
                    </x-button-link>
                @endunless

                <x-button-archive-view
                    :archived="$isArchived"
                    :indexRoute="route('pharmacy.warehouse.index')"
                    :archiveRoute="route('pharmacy.warehouse.index', ['archived' => 'true'])"
                    :archivedCount="$archivedCount ?? 0"
                />
            </div>
        </div>
    </x-slot>

    {{-- Scan QR Modal --}}
    <x-scan-qr-modal 
        name="scan-qr" 
        mode="search" 
        :action="route('pharmacy.warehouse.index')"
        :extraParams="array_filter([
            'archived' => request('archived'),
            'category' => request('category')
        ])"
    />

    <div class="space-y-4">
        {{-- Stats Row --}}
        @if(!$isArchived)
        <div class="grid grid-cols-2 md:grid-cols-2 gap-4">
            {{-- Total Items --}}
            <div class="relative overflow-hidden rounded-2xl bg-white border border-gray-100 shadow-sm p-4 flex items-center gap-4">
                <div class="flex-shrink-0 flex items-center justify-center w-11 h-11 rounded-xl bg-theme-from/10">
                    <x-icon name="library" class="w-5 h-5 text-theme-from" />
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total Items</p>
                    <p class="text-2xl font-extrabold text-gray-800">{{ $totalCount }}</p>
                </div>
                <div class="absolute -right-3 -bottom-3 w-16 h-16 rounded-full bg-theme-from/10 opacity-60"></div>
            </div>

            {{-- Expired Items --}}
            <div class="relative overflow-hidden rounded-2xl bg-white border border-gray-100 shadow-sm p-4 flex items-center gap-4 border-l-4 border-l-red-400">
                <div class="flex-shrink-0 flex items-center justify-center w-11 h-11 rounded-xl bg-red-50">
                    <x-icon name="calendar" class="w-5 h-5 text-red-500" />
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Expired Items</p>
                    <p class="text-2xl font-extrabold text-red-600">{{ $expiredCount ?? '—' }}</p>
                </div>
                <div class="absolute -right-3 -bottom-3 w-16 h-16 rounded-full bg-red-50 opacity-60"></div>
            </div>
        </div>
        @endif

        {{-- Table Card --}}
        <div class="rounded-2xl bg-white border border-gray-100 shadow-sm overflow-hidden">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-gray-50/70">
                        <th class="rounded-l-lg px-6 py-3 text-left text-md leading-4 font-semibold text-gray-500 uppercase tracking-wider">#</th>
                        <th class="px-6 py-3 text-left text-md leading-4 font-semibold text-gray-500 uppercase tracking-wider">Item</th>
                        <th class="px-6 py-3 text-left text-md leading-4 font-semibold text-gray-500 uppercase tracking-wider">Company</th>
                        <th class="px-6 py-3 text-left text-md leading-4 font-semibold text-gray-500 uppercase tracking-wider">Qty</th>
                        <th class="px-6 py-3 text-left text-md leading-4 font-semibold text-gray-500 uppercase tracking-wider text-center">QR</th>
                        <th class="px-6 py-3 text-left text-md leading-4 font-semibold text-gray-500 uppercase tracking-wider">Location</th>
                        <th class="px-6 py-3 text-left text-md leading-4 font-semibold text-gray-500 uppercase tracking-wider">Expiry</th>
                        @if($isArchived)
                            <th class="px-6 py-3 text-left text-md leading-4 font-semibold text-gray-500 uppercase tracking-wider">Archive Date</th>
                        @endif
                        <th class="rounded-r-lg px-6 py-3 bg-gray-50 text-right text-md leading-4 font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($items as $item)
                        @php
                            $isExpired = $item->expiry_date && $item->expiry_date->isPast();
                        @endphp
                        <tr class="group hover:bg-theme-from/10 transition-colors duration-150">
                            <td class="px-5 py-3.5 font-semibold text-gray-300">{{ $loop->iteration }}</td>

                            {{-- Name --}}
                            <td class="px-5 py-3.5">
                                @if($isArchived)
                                    <p class="font-semibold text-gray-700">{{ $item->name }}</p>
                                @else
                                    <a href="{{ route('pharmacy.warehouse.show', $item->id) }}"
                                       class="font-semibold text-gray-800 hover:text-theme-from transition-colors duration-150">
                                        {{ $item->name }}
                                    </a>
                                @endif
                                <p class="text-xs text-gray-400 mt-0.5">{{ $item->category }}</p>
                            </td>

                            {{-- Company --}}
                            <td class="px-5 py-3.5">
                                <span class="text-sm text-gray-600">{{ $item->company_name ?? '—' }}</span>
                            </td>

                            {{-- Quantity --}}
                            <td class="px-5 py-3.5">
                                <span class="font-bold text-gray-800">{{ $item->quantity }}</span>
                                <span class="text-xs text-gray-400">{{ $item->type }}</span>
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
                                            <h3 class="text-xl font-bold text-gray-800">{{ $item->name }}</h3>
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
                                <div class="flex items-center gap-1.5 text-gray-500">
                                    <x-icon name="location" class="w-4 h-4 text-gray-300" />
                                    <span class="text-sm">{{ $item->location_in_warehouse ?? '—' }}</span>
                                </div>
                            </td>

                            {{-- Expiry --}}
                            <td class="px-5 py-3.5">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border 
                                    {{ $isExpired ? 'bg-red-50 text-red-600 border-red-200' : 'bg-emerald-50 text-emerald-600 border-emerald-200' }}">
                                    <x-icon name="calendar" class="w-3.5 h-3.5 mr-1" />
                                    {{ $item->expiry_date?->format('M d, Y') ?? '—' }}
                                </span>
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
                                :editRoute="route('pharmacy.warehouse.edit', $item->id)"
                                :deleteRoute="route('pharmacy.warehouse.destroy', $item->id)"
                                :restoreRoute="route('pharmacy.warehouse.restore', $item->id)"
                                :itemName="$item->name"
                            />
                        </tr>
                    @empty
                        <x-message-empty colspan="{{ $isArchived ? 9 : 8 }}">
                            {{ $isArchived ? 'No archived items found' : 'No items found in warehouse' }}
                        </x-message-empty>
                    @endforelse
                </tbody>
            </table>

            @if($items->hasPages())
                <div class="px-5 py-3 border-t border-gray-100 bg-gray-50/50">
                    {{ $items->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
