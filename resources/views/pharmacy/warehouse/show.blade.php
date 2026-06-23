<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between w-full">
            {{-- Left: Back + Breadcrumb --}}
            <div class="flex items-center gap-3">
                <x-button-back href="{{ route('pharmacy.warehouse.index') }}" label="Warehouse" />
                <div class="h-8 w-px bg-gray-200"></div>
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wider font-medium">Pharmacy › Warehouse</p>
                    <h1 class="text-xl font-bold text-gray-800">{{ $item->name }}</h1>
                </div>
            </div>

            {{-- Right: Edit + Archive --}}
            <div class="flex items-center gap-2">
                <x-button-link href="{{ route('pharmacy.warehouse.edit', $item->id) }}">
                    <x-icon name="edit" class="w-4 h-4 mr-1.5" /> Edit
                </x-button-link>
                <form action="{{ route('pharmacy.warehouse.destroy', $item->id) }}" method="POST"
                      onsubmit="return confirm('Are you sure you want to archive this item?')">
                    @csrf @method('DELETE')
                    <x-button-archive>Archive</x-button-archive>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="space-y-5">
        <div class="max-w-full mx-auto bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">

            {{-- Gradient Header --}}
            @php $isExpired = $item->expiry_date && $item->expiry_date->isPast(); @endphp
            <div class="bg-gradient-to-r from-theme-from to-theme-to px-8 py-6">
                <div class="flex items-center gap-5">
                    <div class="w-16 h-16 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center shadow-inner">
                        <x-icon name="library" class="w-8 h-8 text-white" />
                    </div>
                    <div class="flex-1">
                        <h2 class="text-white text-2xl font-bold">{{ $item->name }}</h2>
                        <div class="flex items-center gap-3 mt-1.5 flex-wrap">
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold backdrop-blur-sm border border-white/10
                                {{ $isExpired ? 'bg-red-500/30 text-white' : 'bg-white/20 text-white' }}">
                                <x-icon name="calendar" class="w-3.5 h-3.5" />
                                {{ $isExpired ? 'Expired' : 'Active Inventory' }}
                            </span>
                            @if($item->category)
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-white/20 text-white backdrop-blur-sm border border-white/10">
                                    <x-icon name="document" class="w-3.5 h-3.5" />
                                    {{ $item->category }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Info Cards Row --}}
            <div class="px-8 py-6 border-b border-gray-100">
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    {{-- Quantity --}}
                    <div class="flex items-center gap-3 p-3.5 rounded-xl bg-gray-50/80 border border-gray-100">
                        <div class="flex-shrink-0 flex items-center justify-center w-10 h-10 rounded-xl bg-theme-from/10">
                            <x-icon name="plus" class="w-5 h-5 text-theme-from" />
                        </div>
                        <div class="min-w-0">
                            <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Quantity</p>
                            <p class="text-sm font-bold text-gray-800 truncate">{{ $item->quantity }} {{ $item->type }}</p>
                        </div>
                    </div>

                    {{-- Expiry --}}
                    <div class="flex items-center gap-3 p-3.5 rounded-xl bg-gray-50/80 border border-gray-100">
                        <div class="flex-shrink-0 flex items-center justify-center w-10 h-10 rounded-xl {{ $isExpired ? 'bg-red-50' : 'bg-emerald-50' }}">
                            <x-icon name="calendar" class="w-5 h-5 {{ $isExpired ? 'text-red-500' : 'text-emerald-500' }}" />
                        </div>
                        <div class="min-w-0">
                            <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Expiry Date</p>
                            <p class="text-sm font-medium {{ $isExpired ? 'text-red-600' : 'text-gray-800' }}">
                                {{ $item->expiry_date?->format('M d, Y') ?? '—' }}
                            </p>
                        </div>
                    </div>

                    {{-- Location --}}
                    <div class="flex items-center gap-3 p-3.5 rounded-xl bg-gray-50/80 border border-gray-100">
                        <div class="flex-shrink-0 flex items-center justify-center w-10 h-10 rounded-xl bg-theme-from/10">
                            <x-icon name="location" class="w-5 h-5 text-theme-from" />
                        </div>
                        <div class="min-w-0">
                            <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Storage Location</p>
                            <p class="text-sm font-medium text-gray-800 truncate">{{ $item->location_in_warehouse ?? '—' }}</p>
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

                    {{-- Supplier --}}
                    <div class="flex items-center gap-3 p-3.5 rounded-xl bg-gray-50/80 border border-gray-100">
                        <div class="flex-shrink-0 flex items-center justify-center w-10 h-10 rounded-xl bg-theme-from/10">
                            <x-icon name="truck" class="w-5 h-5 text-theme-from" />
                        </div>
                        <div class="min-w-0">
                            <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Supplier</p>
                            <p class="text-sm font-medium text-gray-800 truncate">{{ $item->supplier->name ?? '—' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Main Content Grid --}}
            <div class="px-8 py-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    {{-- Details --}}
                    <div>
                        <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-4">Item Details</h3>
                        <div class="p-4 rounded-2xl bg-gray-50 border border-gray-100 space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-semibold text-gray-500">Item Name</span>
                                <span class="text-sm font-bold text-gray-800">{{ $item->name }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-semibold text-gray-500">Company</span>
                                <span class="text-sm text-gray-800">{{ $item->company_name ?? '—' }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-semibold text-gray-500">Category</span>
                                <span class="text-sm text-gray-800">{{ $item->category ?? '—' }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-semibold text-gray-500">Unit Type</span>
                                <span class="text-sm text-gray-800">{{ $item->type ?? '—' }}</span>
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

                    {{-- Notes & System Info --}}
                    <div class="space-y-6">
                        <div>
                            <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-4">Notes</h3>
                            <div class="p-5 rounded-2xl bg-amber-50/50 border border-amber-100 min-h-[140px]">
                                @if($item->notes)
                                    <p class="text-gray-700 text-sm leading-relaxed whitespace-pre-line">{{ $item->notes }}</p>
                                @else
                                    <div class="flex flex-col items-center justify-center h-full text-gray-400 py-4 text-center">
                                        <x-icon name="document" class="w-8 h-8 opacity-20 mb-2" />
                                        <p class="text-xs italic">No notes added</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="p-3.5 rounded-xl bg-gray-50 border border-gray-100">
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Date Added</p>
                                <p class="text-xs font-medium text-gray-700 mt-0.5">{{ $item->created_at->format('M d, Y') }}</p>
                            </div>
                            <div class="p-3.5 rounded-xl bg-gray-50 border border-gray-100">
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Last Update</p>
                                <p class="text-xs font-medium text-gray-700 mt-0.5">{{ $item->updated_at->format('M d, Y') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
