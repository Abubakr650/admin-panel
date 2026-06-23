<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between w-full">
            {{-- Left: Back + Breadcrumb --}}
            <div class="flex items-center gap-3">
                <x-button-back href="{{ route('pharmacy.suppliers.index') }}" label="Suppliers" />
                <div class="h-8 w-px bg-gray-200"></div>
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wider font-medium">Pharmacy › Suppliers</p>
                    <h1 class="text-xl font-bold text-gray-800">{{ $item->name }}</h1>
                </div>
            </div>

            {{-- Right: Edit + Archive --}}
            <div class="flex items-center gap-2">
                <x-button-link href="{{ route('pharmacy.suppliers.edit', $item->id) }}">
                    <x-icon name="edit" class="w-4 h-4 mr-1.5" /> Edit
                </x-button-link>
                <form action="{{ route('pharmacy.suppliers.destroy', $item->id) }}" method="POST"
                      onsubmit="return confirm('Archive this supplier?')">
                    @csrf @method('DELETE')
                    <x-button-archive>Archive</x-button-archive>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="space-y-5">
        <div class="max-w-full mx-auto bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">

            {{-- Gradient Header --}}
            <div class="bg-gradient-to-r from-theme-from to-theme-to px-8 py-6">
                <div class="flex items-center gap-5">
                    <div class="w-16 h-16 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center shadow-inner">
                        <x-icon name="truck" class="w-8 h-8 text-white" />
                    </div>
                    <div class="flex-1">
                        <h2 class="text-white text-2xl font-bold">{{ $item->name }}</h2>
                        <div class="flex items-center gap-3 mt-1.5 flex-wrap">
                            @if($item->country)
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-white/20 text-white backdrop-blur-sm border border-white/10">
                                    <x-icon name="location" class="w-3.5 h-3.5" />
                                    {{ $item->country }}
                                </span>
                            @endif
                            @if($item->phone)
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-white/20 text-white backdrop-blur-sm border border-white/10">
                                    <x-icon name="phone" class="w-3.5 h-3.5" />
                                    {{ $item->phone }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Info Cards Row --}}
            <div class="px-8 py-6 border-b border-gray-100">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

                    {{-- Phone --}}
                    <div class="flex items-center gap-3 p-3.5 rounded-xl bg-gray-50/80 border border-gray-100">
                        <div class="flex-shrink-0 flex items-center justify-center w-10 h-10 rounded-xl bg-theme-from/10">
                            <x-icon name="phone" class="w-5 h-5 text-theme-from" />
                        </div>
                        <div class="min-w-0">
                            <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Phone</p>
                            <p class="text-sm font-medium text-gray-800 truncate">{{ $item->phone ?? '—' }}</p>
                        </div>
                    </div>

                    {{-- Email --}}
                    <div class="flex items-center gap-3 p-3.5 rounded-xl bg-gray-50/80 border border-gray-100">
                        <div class="flex-shrink-0 flex items-center justify-center w-10 h-10 rounded-xl bg-theme-from/10">
                            <x-icon name="mail" class="w-5 h-5 text-theme-from" />
                        </div>
                        <div class="min-w-0">
                            <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Email</p>
                            <p class="text-sm font-medium text-gray-800 truncate">{{ $item->email ?? '—' }}</p>
                        </div>
                    </div>

                    {{-- Country --}}
                    <div class="flex items-center gap-3 p-3.5 rounded-xl bg-gray-50/80 border border-gray-100">
                        <div class="flex-shrink-0 flex items-center justify-center w-10 h-10 rounded-xl bg-theme-from/10">
                            <x-icon name="location" class="w-5 h-5 text-theme-from" />
                        </div>
                        <div class="min-w-0">
                            <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Country</p>
                            <p class="text-sm font-medium text-gray-800 truncate">{{ $item->country ?? '—' }}</p>
                        </div>
                    </div>

                    {{-- Added Date --}}
                    <div class="flex items-center gap-3 p-3.5 rounded-xl bg-gray-50/80 border border-gray-100">
                        <div class="flex-shrink-0 flex items-center justify-center w-10 h-10 rounded-xl bg-theme-from/10">
                            <x-icon name="calendar" class="w-5 h-5 text-theme-from" />
                        </div>
                        <div class="min-w-0">
                            <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Added</p>
                            <p class="text-sm font-medium text-gray-800 truncate">{{ $item->created_at->format('M d, Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Main Content Grid --}}
            <div class="px-8 py-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                    {{-- Left: Supplier Details --}}
                    <div class="space-y-6">
                        <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-4">Supplier Details</h3>
                        <div class="p-4 rounded-2xl bg-gray-50 border border-gray-100">
                            <div class="space-y-2.5">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-semibold text-gray-500">Name</span>
                                    <span class="text-sm font-bold text-gray-800">{{ $item->name }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-semibold text-gray-500">Phone</span>
                                    <span class="text-sm font-medium text-gray-800">{{ $item->phone ?? '—' }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-semibold text-gray-500">Email</span>
                                    <span class="text-sm font-medium text-gray-800">{{ $item->email ?? '—' }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-semibold text-gray-500">Country</span>
                                    <span class="text-sm font-medium text-gray-800">{{ $item->country ?? '—' }}</span>
                                </div>
                                @if($item->address)
                                <div class="pt-2 border-t border-gray-200">
                                    <span class="text-xs font-semibold text-gray-500 block mb-1">Address</span>
                                    <p class="text-sm font-medium text-gray-800 leading-relaxed">{{ $item->address }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Right: Notes + System Info --}}
                    <div class="space-y-6">
                        <div>
                            <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-4">Notes</h3>
                            <div class="p-5 rounded-2xl bg-amber-50/50 border border-amber-100 min-h-[140px]">
                                @if($item->notes)
                                    <p class="text-gray-700 text-sm leading-relaxed whitespace-pre-line">{{ $item->notes }}</p>
                                @else
                                    <div class="flex flex-col items-center justify-center h-full text-gray-400 py-4">
                                        <x-icon name="document" class="w-8 h-8 opacity-20 mb-2" />
                                        <p class="text-xs italic">No notes added</p>
                                    </div>
                                @endif
                            </div>
                        </div>

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
                    <a href="{{ route('pharmacy.suppliers.show', ['supplier' => $item->id, 'tab' => 'batches']) }}"
                       class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-150
                              {{ $activeTab == 'batches' ? 'border-theme-from text-theme-from' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        <x-icon name="document" class="w-4 h-4 inline-block mr-1.5 -mt-0.5" />
                        Pharmacy Batches
                        <span class="ml-1.5 px-2 py-0.5 rounded-full text-xs font-bold
                                     {{ $activeTab == 'batches' ? 'bg-theme-from/10 text-theme-from' : 'bg-gray-100 text-gray-500' }}">
                            {{ $counts['batches'] }}
                        </span>
                    </a>
                    <a href="{{ route('pharmacy.suppliers.show', ['supplier' => $item->id, 'tab' => 'warehouse']) }}"
                       class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-150
                              {{ $activeTab == 'warehouse' ? 'border-theme-from text-theme-from' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        <x-icon name="library" class="w-4 h-4 inline-block mr-1.5 -mt-0.5" />
                        Warehouse Items
                        <span class="ml-1.5 px-2 py-0.5 rounded-full text-xs font-bold
                                     {{ $activeTab == 'warehouse' ? 'bg-theme-from/10 text-theme-from' : 'bg-gray-100 text-gray-500' }}">
                            {{ $counts['warehouse'] }}
                        </span>
                    </a>
                </nav>
            </div>

            {{-- ── Tab Content ─────────────────────────────────── --}}
            <div class="px-8 py-8">
                
                {{-- ═══ Batches Tab ═══ --}}
                @if($activeTab == 'batches')
                    <div class="block">
                        <x-search-bar :action="route('pharmacy.suppliers.show', $item->id)" placeholder="Search by batch number or item name..." :extraParams="['tab' => 'batches']">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('pharmacy.batches.index', ['supplier_id' => $item->id]) }}"
                                   class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-theme-from text-white text-xs font-bold shadow-md shadow-theme-from/20 hover:scale-[1.02] transition-all">
                                    <x-icon name="arrow-right" class="w-3.5 h-3.5" />
                                    View in Batches List
                                </a>
                            </div>
                        </x-search-bar>

                        <div class="mt-6 overflow-x-auto">
                            <table class="min-w-full rounded-xl overflow-hidden border border-gray-100">
                                <thead>
                                    <tr class="bg-gray-50/50">
                                        <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest">Batch Info</th>
                                        <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest">Pharmacy Item</th>
                                        <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest text-center">Qty</th>
                                        <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest">Expiry</th>
                                        <th class="px-6 py-4 text-right text-[11px] font-bold text-gray-400 uppercase tracking-widest">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 bg-white">
                                    @forelse($batches as $batch)
                                        <tr class="group hover:bg-gray-50/80 transition-colors">
                                            <td class="px-6 py-4">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-9 h-9 rounded-lg bg-theme-from/10 flex items-center justify-center text-theme-from">
                                                        <x-icon name="document" class="w-5 h-5" />
                                                    </div>
                                                    <span class="text-sm font-bold text-gray-800">#{{ $batch->batch_number }}</span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="flex items-center gap-2">
                                                    <x-icon name="pill" class="w-4 h-4 text-gray-400" />
                                                    <span class="text-sm font-medium text-gray-700 truncate max-w-[200px]">{{ $batch->pharmacyItem->commercial_name ?? '—' }}</span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                <span class="text-sm font-bold text-gray-800 bg-gray-100 px-2 py-1 rounded-md">{{ $batch->quantity }}</span>
                                            </td>
                                            <td class="px-6 py-4">
                                                <span class="text-[11px] font-bold text-red-500 bg-red-50 px-2 py-1 rounded-md flex items-center gap-1 w-fit">
                                                    <x-icon name="calendar" class="w-3.5 h-3.5" />
                                                    {{ $batch->expiry_date }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-right">
                                                <a href="{{ route('pharmacy.batches.show', $batch->id) }}" 
                                                   class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-theme-from bg-theme-from/10 hover:bg-theme-from hover:text-white transition-all">
                                                    <x-icon name="eye" class="w-4 h-4" />
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr class="bg-gray-50/20">
                                            <td colspan="5" class="px-6 py-12 text-center text-gray-400 italic text-sm">No batches found</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @if($batches->hasPages())
                            <div class="mt-6">{{ $batches->links() }}</div>
                        @endif
                    </div>
                @endif

                {{-- ═══ Warehouse Tab ═══ --}}
                @if($activeTab == 'warehouse')
                    <div class="block">
                        <x-search-bar :action="route('pharmacy.suppliers.show', $item->id)" placeholder="Search by item name or company..." :extraParams="['tab' => 'warehouse']">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('pharmacy.warehouse.index', ['supplier_id' => $item->id]) }}"
                                   class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-theme-from text-white text-xs font-bold shadow-md shadow-theme-from/20 hover:scale-[1.02] transition-all">
                                    <x-icon name="arrow-right" class="w-3.5 h-3.5" />
                                    View in Warehouse List
                                </a>
                            </div>
                        </x-search-bar>

                        <div class="mt-6 overflow-x-auto">
                            <table class="min-w-full rounded-xl overflow-hidden border border-gray-100">
                                <thead>
                                    <tr class="bg-gray-50/50">
                                        <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest">Item Name</th>
                                        <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest">Category</th>
                                        <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest text-center">Stock</th>
                                        <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest">Storage</th>
                                        <th class="px-6 py-4 text-right text-[11px] font-bold text-gray-400 uppercase tracking-widest">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 bg-white">
                                    @forelse($warehouseItems as $whItem)
                                        <tr class="group hover:bg-gray-50/80 transition-colors">
                                            <td class="px-6 py-4 text-sm font-bold text-gray-800">{{ $whItem->name }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-600">{{ $whItem->category ?? '—' }}</td>
                                            <td class="px-6 py-4 text-center">
                                                <span class="text-sm font-bold {{ $whItem->total_quantity > 10 ? 'text-emerald-600' : 'text-amber-600' }}">
                                                    {{ $whItem->total_quantity }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-500 italic">{{ $whItem->storage_location ?? '—' }}</td>
                                            <td class="px-6 py-4 text-right">
                                                <a href="{{ route('pharmacy.warehouse.show', $whItem->id) }}" 
                                                   class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-theme-from bg-theme-from/10 hover:bg-theme-from hover:text-white transition-all">
                                                    <x-icon name="eye" class="w-4 h-4" />
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr class="bg-gray-50/20">
                                            <td colspan="5" class="px-6 py-12 text-center text-gray-400 italic text-sm">No warehouse items found</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @if($warehouseItems->hasPages())
                            <div class="mt-6">{{ $warehouseItems->links() }}</div>
                        @endif
                    </div>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>
