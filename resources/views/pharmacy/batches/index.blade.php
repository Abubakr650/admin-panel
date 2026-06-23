<x-app-layout>
@php $isArchived = request()->input('archived') === 'true'; @endphp
    <x-slot name="header">
        <div class="flex items-center justify-between w-full gap-4">
            {{-- Left: Title + Search --}}
            <div class="flex items-center gap-3 flex-1 min-w-0">
                <div class="shrink-0">
                    <p class="text-xs text-gray-400 uppercase tracking-widest font-semibold">Pharmacy</p>
                    <h1 class="text-lg font-bold text-gray-800 leading-tight">
                        {{ $isArchived ? 'Archived Batches' : 'Pharmacy Batches' }}
                    </h1>
                </div>
                <div class="h-8 w-px bg-gray-200 shrink-0"></div>
                <div class="w-64">
                    <x-search-bar
                        action="{{ route('pharmacy.batches.index') }}"
                        placeholder="Search by batch #, item, supplier…"
                        :extraParams="array_filter(['archived' => request('archived')])"
                    />
                </div>
                
                {{-- Expired Items Filter --}}
                @unless($isArchived)
                @php $activeExpired = request()->input('expired', 'all'); @endphp
                <div class="flex items-center gap-1 bg-gray-100 rounded-xl p-1 shrink-0">
                    @foreach(['all' => 'All', 'expired' => 'Expired', 'not_expired' => 'Not Expired'] as $value => $label)
                        @php
                            $isActive = $activeExpired === $value;
                            $href = route('pharmacy.batches.index', array_filter([
                                'search'  => request()->input('search'),
                                'expired' => $value === 'all' ? null : $value,
                                'archived'=> request()->input('archived'),
                            ]));
                        @endphp
                        <a href="{{ $href }}"
                           class="px-3 py-1 rounded-lg text-sm font-semibold transition-all duration-150
                                  {{ $isActive
                                      ? 'bg-white text-gray-800 shadow-sm'
                                      : 'text-gray-500 hover:text-gray-700' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>
                @endunless
            </div>

            {{-- Right: Actions --}}
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
                    <x-button-link href="{{ route('pharmacy.batches.create') }}">
                        <x-icon name="plus" class="w-4 h-4" />
                        New Batch
                    </x-button-link>
                @endunless

                <x-button-archive-view
                    :archived="$isArchived"
                    :indexRoute="route('pharmacy.batches.index')"
                    :archiveRoute="route('pharmacy.batches.index', ['archived' => 'true'])"
                    :archivedCount="$archivedCount ?? 0"
                />
            </div>
        </div>
    </x-slot>

    {{-- Scan QR Modal --}}
    <x-scan-qr-modal 
        name="scan-qr" 
        mode="search" 
        :action="route('pharmacy.batches.index')"
        :extraParams="array_filter([
            'archived' => request('archived')
        ])"
    />

    <div class="space-y-4">
        {{-- Stats Row --}}
        @if(!$isArchived)
        <div class="grid grid-cols-2 sm:grid-cols-2 gap-4">
            <div class="relative overflow-hidden rounded-2xl bg-white border border-gray-100 shadow-sm p-4 flex items-center gap-4">
                <div class="flex-shrink-0 flex items-center justify-center w-11 h-11 rounded-xl bg-theme-from/10">
                    <x-icon name="document" class="w-5 h-5 text-theme-from" />
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total Batches</p>
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

        {{-- Table --}}
        <div class="rounded-2xl bg-white border border-gray-100 shadow-sm overflow-hidden">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-gray-50/70">
                        <th class="rounded-l-lg px-6 py-3 text-left text-md leading-4 font-semibold text-gray-500 uppercase tracking-wider">#</th>
                        <th class="px-6 py-3 text-left text-md leading-4 font-semibold text-gray-500 uppercase tracking-wider">Batch #</th>
                        <th class="px-6 py-3 text-left text-md leading-4 font-semibold text-gray-500 uppercase tracking-wider">Item</th>
                        <th class="px-6 py-3 text-left text-md leading-4 font-semibold text-gray-500 uppercase tracking-wider">Supplier</th>
                        <th class="px-6 py-3 text-left text-md leading-4 font-semibold text-gray-500 uppercase tracking-wider">Qty / Rem.</th>
                        <th class="px-6 py-3 text-left text-md leading-4 font-semibold text-gray-500 uppercase tracking-wider text-center">QR</th>
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

                            {{-- Batch Number --}}
                            <td class="px-5 py-3.5">
                                @if($isArchived)
                                    <span class="font-bold text-gray-700">{{ $item->batch_number }}</span>
                                @else
                                    <a href="{{ route('pharmacy.batches.show', $item->id) }}"
                                       class="font-bold text-gray-800 hover:text-theme-from transition-colors duration-150">
                                        {{ $item->batch_number }}
                                    </a>
                                @endif
                            </td>

                            {{-- Item --}}
                            <td class="px-5 py-3.5">
                                <p class="text-sm font-medium text-gray-700">{{ $item->pharmacyItem->commercial_name ?? '—' }}</p>
                            </td>

                            {{-- Supplier --}}
                            <td class="px-5 py-3.5">
                                @if($item->supplier)
                                    <a href="{{ route('pharmacy.suppliers.show', $item->supplier_id) }}"
                                       class="text-sm text-theme-from hover:underline">
                                        {{ $item->supplier->name }}
                                    </a>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>

                            {{-- Qty / Remaining --}}
                            <td class="px-5 py-3.5">
                                <span class="font-semibold text-gray-800">{{ $item->quantity }}</span>
                                @if($item->remaining_quantity !== null)
                                    <span class="text-gray-400 text-xs"> / {{ $item->remaining_quantity }} rem.</span>
                                @endif
                            </td>

                            {{-- QR Code View --}}
                            <td class="px-5 py-3.5 text-center">
                                @if($item->pharmacyItem && $item->pharmacyItem->qr_code)
                                    <button x-on:click="$dispatch('open-modal', 'qr-{{ $item->id }}')" 
                                        class="p-1.5 rounded-lg bg-gray-50 text-gray-400 hover:text-theme-from hover:bg-theme-from/10 transition-all duration-200"
                                        title="Item QR: {{ $item->pharmacyItem->qr_code }}">
                                        <x-icon name="qr-code" class="w-5 h-5" />
                                    </button>

                                    {{-- Row-specific QR Modal --}}
                                    <x-modal name="qr-{{ $item->id }}" focusable>
                                        <div class="p-8 text-center text-md">
                                            <div class="mb-4 flex justify-center">
                                                <div class="p-4 bg-white rounded-2xl border-2 border-dashed border-gray-100 shadow-sm">
                                                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode($item->pharmacyItem->qr_code) }}" 
                                                         alt="QR Code" class="w-48 h-48">
                                                </div>
                                            </div>
                                            <h3 class="text-xl font-bold text-gray-800">{{ $item->pharmacyItem->commercial_name }}</h3>
                                            <p class="text-xs text-gray-400 uppercase tracking-widest font-semibold mt-1">Batch #{{ $item->batch_number }}</p>
                                            <p class="text-sm font-mono text-gray-500 mt-2">{{ $item->pharmacyItem->qr_code }}</p>
                                            
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
                                :editRoute="route('pharmacy.batches.edit', $item->id)"
                                :deleteRoute="route('pharmacy.batches.destroy', $item->id)"
                                :restoreRoute="route('pharmacy.batches.restore', $item->id)"
                                :itemName="'Batch #' . $item->batch_number"
                            />
                        </tr>
                    @empty
                        <x-message-empty colspan="{{ $isArchived ? 9 : 8 }}">
                            {{ $isArchived ? 'No archived batches found' : 'No batches found' }}
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
