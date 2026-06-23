<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between w-full">
            {{-- Left: Back + Breadcrumb --}}
            <div class="flex items-center gap-3">
                <x-button-back href="{{ route('pharmacy.items.index') }}" label="Items" />
                <div class="h-8 w-px bg-gray-200"></div>
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wider font-medium">Pharmacy › Items</p>
                    <h1 class="text-xl font-bold text-gray-800">{{ $item->commercial_name }}</h1>
                </div>
            </div>
            {{-- Right: Action Buttons --}}
            <div class="flex items-center gap-2">
                <x-button-link href="{{ route('pharmacy.items.edit', $item->id) }}">
                    <x-icon name="edit" class="w-4 h-4 mr-1.5" />
                    Edit
                </x-button-link>
                <form action="{{ route('pharmacy.items.destroy', $item->id) }}" method="POST"
                      onsubmit="return confirm('Are you sure you want to archive this item?')">
                    @csrf @method('DELETE')
                    <x-button-archive>Archive</x-button-archive>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="space-y-5">
        {{-- ── Main Wrapper Card ─────────────────────────────────────── --}}
        <div class="max-w-full mx-auto bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">

            {{-- ── Item Profile Header ────────────────────────────── --}}
            <div class="bg-gradient-to-r from-theme-from to-theme-to px-8 py-6">
                <div class="flex items-center gap-5">
                    {{-- Icon --}}
                    <div class="w-16 h-16 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center shadow-inner">
                        <x-icon name="clipboard" class="w-8 h-8 text-white" />
                    </div>
                    <div class="flex-1">
                        <h2 class="text-white text-2xl font-bold">{{ $item->commercial_name }}</h2>
                        <div class="flex items-center gap-3 mt-1.5 flex-wrap">
                            {{-- Scientific Name badge --}}
                            @if($item->scientific_name)
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-white/20 text-white backdrop-blur-sm border border-white/10">
                                    <x-icon name="academic-cap" class="w-3.5 h-3.5" />
                                    {{ $item->scientific_name }}
                                </span>
                            @endif
                            {{-- Form badge --}}
                            @if($item->form)
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-white/20 text-white backdrop-blur-sm border border-white/10">
                                    <x-icon name="filter" class="w-3.5 h-3.5" />
                                    {{ $item->form }}
                                </span>
                            @endif
                            {{-- Company --}}
                            @if($item->company_name)
                                <span class="text-sky-100 text-sm">{{ $item->company_name }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Item Info Cards ─────────────────────────────────── --}}
            <div class="px-8 py-6 border-b border-gray-100">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    {{-- Category --}}
                    <div class="flex items-center gap-3 p-3.5 rounded-xl bg-gray-50/80 border border-gray-100">
                        <div class="flex-shrink-0 flex items-center justify-center w-10 h-10 rounded-xl bg-theme-from/10">
                            <x-icon name="document" class="w-5 h-5 text-theme-from" />
                        </div>
                        <div class="min-w-0">
                            <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Category</p>
                            <p class="text-sm font-medium text-gray-800 truncate">{{ $item->category ?? '—' }}</p>
                        </div>
                    </div>
                    {{-- Form --}}
                    <div class="flex items-center gap-3 p-3.5 rounded-xl bg-gray-50/80 border border-gray-100">
                        <div class="flex-shrink-0 flex items-center justify-center w-10 h-10 rounded-xl bg-theme-from/10">
                            <x-icon name="filter" class="w-5 h-5 text-theme-from" />
                        </div>
                        <div class="min-w-0">
                            <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Form</p>
                            <p class="text-sm font-medium text-gray-800 truncate">{{ $item->form ?? '—' }}</p>
                        </div>
                    </div>
                    {{-- Location --}}
                    <div class="flex items-center gap-3 p-3.5 rounded-xl bg-gray-50/80 border border-gray-100">
                        <div class="flex-shrink-0 flex items-center justify-center w-10 h-10 rounded-xl bg-theme-to/10">
                            <x-icon name="location" class="w-5 h-5 text-theme-to" />
                        </div>
                        <div class="min-w-0">
                            <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Location</p>
                            <p class="text-sm font-medium text-gray-800 truncate">{{ $item->location_in_pharmacy ?? '—' }}</p>
                        </div>
                    </div>
                    {{-- QR Code --}}
                    <div class="flex items-center gap-3 p-3.5 rounded-xl bg-gray-50/80 border border-gray-100">
                        <div class="flex-shrink-0 flex items-center justify-center w-10 h-10 rounded-xl bg-theme-from/10">
                            <x-icon name="qr-code" class="w-5 h-5 text-theme-from" />
                        </div>
                        <div class="min-w-0">
                            <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">QR Code</p>
                            <p class="text-sm font-medium text-gray-800 truncate">{{ $item->qr_code ?? '—' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Main Content ─────────────────────────────────── --}}
            <div class="px-8 py-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                    {{-- Left Column: Item Details --}}
                    <div class="space-y-6">
                        {{-- Company Info --}}
                        <div>
                            <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-4">Item Details</h3>
                            <div class="space-y-3">
                                <div class="p-4 rounded-2xl bg-gray-50 border border-gray-100">
                                    <div class="space-y-2.5">
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs font-semibold text-gray-500">Commercial Name</span>
                                            <span class="text-sm font-bold text-gray-800">{{ $item->commercial_name }}</span>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs font-semibold text-gray-500">Scientific Name</span>
                                            <span class="text-sm text-gray-700">{{ $item->scientific_name ?? '—' }}</span>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs font-semibold text-gray-500">Company</span>
                                            <span class="text-sm text-gray-700">{{ $item->company_name ?? '—' }}</span>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs font-semibold text-gray-500">Form</span>
                                            <span class="text-sm text-gray-700">{{ $item->form ?? '—' }}</span>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs font-semibold text-gray-500">Category</span>
                                            <span class="text-sm text-gray-700">{{ $item->category ?? '—' }}</span>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs font-semibold text-gray-500">Location</span>
                                            <span class="text-sm text-gray-700">{{ $item->location_in_pharmacy ?? '—' }}</span>
                                        </div>
                                        @if($item->qr_code)
                                            <div class="pt-4 flex flex-col items-center border-t border-gray-100 mt-2">
                                                <div class="p-2 bg-white rounded-lg border border-gray-200">
                                                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data={{ urlencode($item->qr_code) }}" 
                                                         alt="QR Code" class="w-24 h-24">
                                                </div>
                                                <span class="text-[10px] font-mono text-gray-400 mt-2 uppercase tracking-widest">{{ $item->qr_code }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Right Column: Notes + System Info --}}
                    <div class="space-y-6">
                        {{-- Notes --}}
                        <div>
                            <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-4">Notes</h3>
                            <div class="p-5 rounded-2xl bg-amber-50/50 border border-amber-100 min-h-[140px]">
                                @if($item->notes)
                                    <p class="text-gray-700 text-sm leading-relaxed whitespace-pre-line">
                                        {{ $item->notes }}
                                    </p>
                                @else
                                    <div class="flex flex-col items-center justify-center h-full text-gray-400 py-4">
                                        <x-icon name="document" class="w-8 h-8 opacity-20 mb-2" />
                                        <p class="text-xs italic">No notes added for this item</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- System Info --}}
                        <div class="grid grid-cols-2 gap-4">
                            <div class="p-3.5 rounded-xl bg-gray-50 border border-gray-100">
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Created</p>
                                <p class="text-xs font-medium text-gray-700 mt-0.5">{{ $item->created_at->format('M d, Y') }}</p>
                            </div>
                            <div class="p-3.5 rounded-xl bg-gray-50 border border-gray-100">
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Last Update</p>
                                <p class="text-xs font-medium text-gray-700 mt-0.5">{{ $item->updated_at->format('M d, Y') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

            {{-- ── Tabs Navigation ── --}}
            <div class="border-b border-gray-200 px-8">
                <nav class="-mb-px flex space-x-8">
                    <a href="{{ route('pharmacy.items.show', ['item' => $item->id, 'tab' => 'batches']) }}"
                       class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-150
                              {{ $activeTab == 'batches' ? 'border-theme-from text-theme-from' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        <x-icon name="document" class="w-4 h-4 inline-block mr-1.5 -mt-0.5" />
                        Batches in Stock
                        <span class="ml-1.5 px-2 py-0.5 rounded-full text-xs font-bold
                                     {{ $activeTab == 'batches' ? 'bg-theme-from/10 text-theme-from' : 'bg-gray-100 text-gray-500' }}">
                            {{ $counts['batches'] }}
                        </span>
                    </a>
                </nav>
            </div>

            {{-- ── Tab Content ─────────────────────────────────── --}}
            <div class="px-8 py-8">
                @if($activeTab == 'batches')
                    <div class="block">
                        <x-search-bar :action="route('pharmacy.items.show', $item->id)" placeholder="Search by batch number or supplier..." :extraParams="['tab' => 'batches']">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('pharmacy.batches.index', ['item_id' => $item->id]) }}"
                                   class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-theme-from text-white text-xs font-bold shadow-md shadow-theme-from/20 hover:scale-[1.02] transition-all">
                                    <x-icon name="plus" class="w-3.5 h-3.5" />
                                    Manage Batches
                                </a>
                            </div>
                        </x-search-bar>

                        <div class="mt-6 overflow-x-auto">
                            <table class="min-w-full rounded-xl overflow-hidden border border-gray-100">
                                <thead>
                                    <tr class="bg-gray-50/50">
                                        <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest">Batch Info</th>
                                        <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest">Supplier</th>
                                        <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest text-center">Quantity</th>
                                        <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest">Dates</th>
                                        <th class="px-6 py-4 text-right text-[11px] font-bold text-gray-400 uppercase tracking-widest">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 bg-white">
                                    @forelse($batches as $batch)
                                        <tr class="group hover:bg-gray-50/80 transition-colors">
                                            <td class="px-6 py-4">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-9 h-9 rounded-lg bg-theme-from/10 flex items-center justify-center group-hover:bg-theme-from/20 transition-colors text-theme-from">
                                                        <x-icon name="document" class="w-5 h-5" />
                                                    </div>
                                                    <div>
                                                        <p class="text-sm font-bold text-gray-800">#{{ $batch->batch_number }}</p>
                                                        <p class="text-[11px] text-gray-400 font-medium">Batch ID: {{ substr($batch->id, 0, 8) }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="flex items-center gap-2">
                                                    <x-icon name="truck" class="w-4 h-4 text-gray-400" />
                                                    <span class="text-sm font-medium text-gray-700">{{ $batch->supplier->name ?? 'No Supplier' }}</span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                <div class="inline-flex flex-col items-center px-3 py-1 rounded-lg bg-gray-50 border border-gray-100 group-hover:border-theme-from/20 transition-all">
                                                    <span class="text-sm font-bold text-gray-800">{{ $batch->quantity }}</span>
                                                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-tight">Units</span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="space-y-1">
                                                    <div class="flex items-center gap-1.5 text-[11px] font-medium text-gray-500">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                                        P: {{ $batch->production_date }}
                                                    </div>
                                                    <div class="flex items-center gap-1.5 text-[11px] font-bold text-red-500 bg-red-50 w-fit px-1.5 py-0.5 rounded-md">
                                                        <x-icon name="calendar" class="w-3 h-3" />
                                                        E: {{ $batch->expiry_date }}
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 text-right">
                                                <a href="{{ route('pharmacy.batches.show', $batch->id) }}" 
                                                   class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-theme-from bg-theme-from/10 border border-theme-from/10 hover:bg-theme-from hover:text-white transition-all shadow-sm">
                                                    <x-icon name="eye" class="w-4 h-4" />
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-6 py-12 text-center bg-gray-50/30">
                                                <div class="flex flex-col items-center gap-3">
                                                    <div class="w-16 h-16 rounded-2xl bg-gray-100 flex items-center justify-center text-gray-300">
                                                        <x-icon name="document" class="w-8 h-8" />
                                                    </div>
                                                    <div>
                                                        <p class="text-gray-500 font-bold">No batches found</p>
                                                        <p class="text-[11px] text-gray-400 mt-1">Try adjusting your search or add a new batch</p>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if($batches->hasPages())
                            <div class="mt-8 px-2">
                                {{ $batches->links() }}
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
