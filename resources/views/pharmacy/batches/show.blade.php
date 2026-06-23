<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between w-full">
            {{-- Left: Back + Breadcrumb --}}
            <div class="flex items-center gap-3">
                <x-button-back href="{{ route('pharmacy.batches.index') }}" label="Batches" />
                <div class="h-8 w-px bg-gray-200"></div>
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wider font-medium">Pharmacy › Batches</p>
                    <h1 class="text-xl font-bold text-gray-800">Batch #{{ $item->batch_number }}</h1>
                </div>
            </div>

            {{-- Right: Edit + Archive --}}
            <div class="flex items-center gap-2">
                <x-button-link href="{{ route('pharmacy.batches.edit', $item->id) }}">
                    <x-icon name="edit" class="w-4 h-4 mr-1.5" /> Edit
                </x-button-link>
                <form action="{{ route('pharmacy.batches.destroy', $item->id) }}" method="POST"
                      onsubmit="return confirm('Archive this batch?')">
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
                        <x-icon name="document" class="w-8 h-8 text-white" />
                    </div>
                    <div class="flex-1">
                        <h2 class="text-white text-2xl font-bold">Batch #{{ $item->batch_number }}</h2>
                        <div class="flex items-center gap-3 mt-1.5 flex-wrap">
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold backdrop-blur-sm border border-white/10
                                {{ $isExpired ? 'bg-red-500/30 text-white' : 'bg-white/20 text-white' }}">
                                <x-icon name="calendar" class="w-3.5 h-3.5" />
                                {{ $isExpired ? 'EXPIRED' : 'ACTIVE' }}
                            </span>
                            @if($item->supplier)
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-white/20 text-white backdrop-blur-sm border border-white/10">
                                    <x-icon name="truck" class="w-3.5 h-3.5" />
                                    {{ $item->supplier->name }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Info Cards Row --}}
            <div class="px-8 py-6 border-b border-gray-100">
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">

                    {{-- Item --}}
                    <div class="flex items-center gap-3 p-3.5 rounded-xl bg-gray-50/80 border border-gray-100">
                        <div class="flex-shrink-0 flex items-center justify-center w-10 h-10 rounded-xl bg-theme-from/10">
                            <x-icon name="pill" class="w-5 h-5 text-theme-from" />
                        </div>
                        <div class="min-w-0">
                            <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Item</p>
                            <p class="text-sm font-medium text-gray-800 truncate">{{ $item->pharmacyItem->commercial_name ?? '—' }}</p>
                        </div>
                    </div>

                    {{-- Quantity --}}
                    <div class="flex items-center gap-3 p-3.5 rounded-xl bg-gray-50/80 border border-gray-100">
                        <div class="flex-shrink-0 flex items-center justify-center w-10 h-10 rounded-xl bg-theme-from/10">
                            <x-icon name="plus" class="w-5 h-5 text-theme-from" />
                        </div>
                        <div class="min-w-0">
                            <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Qty / Rem.</p>
                            <p class="text-sm font-bold text-gray-800">{{ $item->quantity }} <span class="font-normal text-gray-400">/ {{ $item->remaining_quantity }}</span></p>
                        </div>
                    </div>

                    {{-- Expiry --}}
                    <div class="flex items-center gap-3 p-3.5 rounded-xl bg-gray-50/80 border border-gray-100">
                        <div class="flex-shrink-0 flex items-center justify-center w-10 h-10 rounded-xl {{ $isExpired ? 'bg-red-50' : 'bg-emerald-50' }}">
                            <x-icon name="calendar" class="w-5 h-5 {{ $isExpired ? 'text-red-500' : 'text-emerald-500' }}" />
                        </div>
                        <div class="min-w-0">
                            <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Expiry</p>
                            <p class="text-sm font-medium {{ $isExpired ? 'text-red-600' : 'text-gray-800' }}">
                                {{ $item->expiry_date?->format('M d, Y') ?? '—' }}
                            </p>
                        </div>
                    </div>

                    {{-- Production --}}
                    <div class="flex items-center gap-3 p-3.5 rounded-xl bg-gray-50/80 border border-gray-100">
                        <div class="flex-shrink-0 flex items-center justify-center w-10 h-10 rounded-xl bg-theme-from/10">
                            <x-icon name="calendar" class="w-5 h-5 text-theme-from" />
                        </div>
                        <div class="min-w-0">
                            <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Production</p>
                            <p class="text-sm font-medium text-gray-800">{{ $item->production_date?->format('M d, Y') ?? '—' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Body --}}
            <div class="px-8 py-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                    {{-- Left: Batch Details --}}
                    <div>
                        <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-4">Batch Details</h3>
                        <div class="p-4 rounded-2xl bg-gray-50 border border-gray-100 space-y-2.5">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-semibold text-gray-500">Batch Number</span>
                                <span class="text-sm font-bold text-gray-800">{{ $item->batch_number }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-semibold text-gray-500">Pharmacy Item</span>
                                @if($item->pharmacyItem)
                                    <a href="{{ route('pharmacy.items.show', $item->pharmacy_item_id) }}"
                                       class="text-sm text-theme-from hover:underline font-medium">
                                        {{ $item->pharmacyItem->commercial_name }}
                                    </a>
                                @else
                                    <span class="text-sm text-gray-400">—</span>
                                @endif
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-semibold text-gray-500">Supplier</span>
                                @if($item->supplier)
                                    <a href="{{ route('pharmacy.suppliers.show', $item->supplier_id) }}"
                                       class="text-sm text-theme-from hover:underline font-medium">
                                        {{ $item->supplier->name }}
                                    </a>
                                @else
                                    <span class="text-sm text-gray-400">—</span>
                                @endif
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-semibold text-gray-500">Initial Quantity</span>
                                <span class="text-sm font-medium text-gray-800">{{ $item->quantity }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-semibold text-gray-500">Remaining Quantity</span>
                                <span class="text-sm font-medium text-gray-800">{{ $item->remaining_quantity ?? '—' }}</span>
                            </div>

                            @if($item->pharmacyItem && $item->pharmacyItem->qr_code)
                            <div class="mt-4 pt-4 border-t border-gray-100 flex flex-col items-center">
                                <div class="p-2 bg-white rounded-xl border border-gray-200 shadow-sm">
                                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ urlencode($item->pharmacyItem->qr_code) }}" 
                                         alt="QR Code" class="w-28 h-28">
                                </div>
                                <p class="mt-2 text-[10px] font-mono text-gray-400 uppercase tracking-widest">{{ $item->pharmacyItem->qr_code }}</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    {{-- Right: Dates + Status --}}
                    <div class="space-y-5">
                        <div>
                            <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-4">Validity</h3>
                            <div class="space-y-3">
                                <div class="flex items-center gap-3 p-3.5 rounded-xl bg-gray-50 border border-gray-100">
                                    <x-icon name="calendar" class="w-4 h-4 text-gray-400" />
                                    <div class="flex-1">
                                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Production Date</p>
                                        <p class="text-sm font-medium text-gray-700">{{ $item->production_date?->format('M d, Y') ?? '—' }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3 p-3.5 rounded-xl border
                                    {{ $isExpired ? 'bg-red-50 border-red-200' : 'bg-emerald-50 border-emerald-200' }}">
                                    <x-icon name="calendar" class="w-4 h-4 {{ $isExpired ? 'text-red-500' : 'text-emerald-500' }}" />
                                    <div class="flex-1">
                                        <p class="text-[10px] font-bold {{ $isExpired ? 'text-red-400' : 'text-emerald-600' }} uppercase tracking-wider">Expiry Date</p>
                                        <p class="text-sm font-bold {{ $isExpired ? 'text-red-600' : 'text-emerald-700' }}">
                                            {{ $item->expiry_date?->format('M d, Y') ?? '—' }}
                                        </p>
                                    </div>
                                    <span class="text-xs font-bold {{ $isExpired ? 'text-red-600' : 'text-emerald-600' }}">
                                        {{ $isExpired ? 'EXPIRED' : 'VALID' }}
                                    </span>
                                </div>
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
            </div>
        </div>
    </div>
</x-app-layout>
