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
                        {{ $isArchived ? 'Archived Suppliers' : 'Suppliers' }}
                    </h1>
                </div>

                {{-- Divider --}}
                <div class="h-8 w-px bg-gray-200 shrink-0"></div>

                {{-- Search Bar --}}
                <div class="w-64">
                    <x-search-bar
                        action="{{ route('pharmacy.suppliers.index') }}"
                        placeholder="Search by any field…"
                        :extraParams="array_filter(['archived' => request('archived')])"
                    />
                </div>
            </div>

            {{-- Right: Action Buttons --}}
            <div class="flex items-center gap-3 shrink-0">
                @unless($isArchived)
                    <x-button-link href="{{ route('pharmacy.suppliers.create') }}">
                        <x-icon name="plus" class="w-4 h-4" />
                        New Supplier
                    </x-button-link>
                @endunless

                <x-button-archive-view
                    :archived="$isArchived"
                    :indexRoute="route('pharmacy.suppliers.index')"
                    :archiveRoute="route('pharmacy.suppliers.index', ['archived' => 'true'])"
                    :archivedCount="$archivedCount ?? 0"
                />
            </div>
        </div>
    </x-slot>

    <div class="space-y-4">
        {{-- Stats Row --}}
        @if(!$isArchived)
        <div class="grid grid-cols-2 sm:grid-cols-2 gap-4">
            {{-- Total Suppliers --}}
            <div class="relative overflow-hidden rounded-2xl bg-white border border-gray-100 shadow-sm p-4 flex items-center gap-4">
                <div class="flex-shrink-0 flex items-center justify-center w-11 h-11 rounded-xl bg-theme-from/10">
                    <x-icon name="truck" class="w-5 h-5 text-theme-from" />
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total</p>
                    <p class="text-2xl font-extrabold text-gray-800">{{ $totalCount }}</p>
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
                        <th class="px-6 py-3 text-left text-md leading-4 font-semibold text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-md leading-4 font-semibold text-gray-500 uppercase tracking-wider">Phone</th>
                        <th class="px-6 py-3 text-left text-md leading-4 font-semibold text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-md leading-4 font-semibold text-gray-500 uppercase tracking-wider">Country</th>
                        @if($isArchived)
                            <th class="px-6 py-3 text-left text-md leading-4 font-semibold text-gray-500 uppercase tracking-wider">Archive Date</th>
                        @endif
                        <th class="rounded-r-lg px-6 py-3 bg-gray-50 text-right text-md leading-4 font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($items as $item)
                        <tr class="group hover:bg-theme-from/10 transition-colors duration-150">
                            <td class="px-5 py-3.5 font-semibold text-gray-300">{{ $loop->iteration }}</td>

                            {{-- Name --}}
                            <td class="px-5 py-3.5">
                                @if($isArchived)
                                    <p class="font-semibold text-gray-700">{{ $item->name }}</p>
                                @else
                                    <a href="{{ route('pharmacy.suppliers.show', $item->id) }}"
                                       class="font-semibold text-gray-800 hover:text-theme-from transition-colors duration-150">
                                        {{ $item->name }}
                                    </a>
                                @endif
                            </td>

                            {{-- Phone --}}
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-1.5 text-gray-600">
                                    @if($item->phone)
                                        <x-icon name="phone" class="w-4 h-4 text-gray-300 flex-shrink-0" />
                                        {{ $item->phone }}
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </div>
                            </td>

                            {{-- Email --}}
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-1.5 text-gray-600">
                                    @if($item->email)
                                        <x-icon name="mail" class="w-4 h-4 text-gray-300 flex-shrink-0" />
                                        {{ $item->email }}
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </div>
                            </td>

                            {{-- Country --}}
                            <td class="px-5 py-3.5">
                                @if($item->country)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-theme-from/10 text-theme-from border border-theme-from/20">
                                        {{ $item->country }}
                                    </span>
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
                                :editRoute="route('pharmacy.suppliers.edit', $item->id)"
                                :deleteRoute="route('pharmacy.suppliers.destroy', $item->id)"
                                :restoreRoute="route('pharmacy.suppliers.restore', $item->id)"
                                :itemName="$item->name"
                            />
                        </tr>
                    @empty
                        <x-message-empty colspan="{{ $isArchived ? 7 : 6 }}">
                            {{ $isArchived ? 'No archived suppliers found' : 'No suppliers found' }}
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
