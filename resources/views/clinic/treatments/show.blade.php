<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between w-full gap-4">
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-widest font-semibold">Clinic › Treatments</p>
                <h1 class="text-xl font-bold text-gray-800">Treatment Details</h1>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('treatments.edit', $treatment->id) }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 rounded-xl text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-all shadow-sm">
                    <x-icon name="pencil" class="w-4 h-4 text-theme-from" /> Edit
                </a>
                <a href="{{ route('treatments.index') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 border border-gray-200 rounded-xl text-sm font-semibold text-gray-600 hover:bg-gray-200 transition-all">
                    <x-icon name="arrow-left" class="w-4 h-4" /> Back
                </a>
            </div>
        </div>
    </x-slot>

    <div class="p-6">
        <div class="max-w-3xl mx-auto space-y-6">

            @php
                $typeColors = [
                    'consultation' => 'bg-indigo-50 text-indigo-600 border-indigo-100',
                    'filling' => 'bg-blue-50 text-blue-600 border-blue-100',
                    'extraction' => 'bg-red-50 text-red-600 border-red-100',
                    'cleaning' => 'bg-teal-50 text-teal-600 border-teal-100',
                    'cosmetic' => 'bg-pink-50 text-pink-600 border-pink-100',
                    'radiology' => 'bg-purple-50 text-purple-600 border-purple-100',
                    'orthodontic_session' => 'bg-cyan-50 text-cyan-600 border-cyan-100',
                    'pharmacy_dispense' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                    'other' => 'bg-gray-50 text-gray-600 border-gray-100',
                ];
                $statusColors = [
                    'completed' => 'bg-green-50 text-green-600 border-green-100',
                    'in_progress' => 'bg-blue-50 text-blue-600 border-blue-100',
                    'planned' => 'bg-amber-50 text-amber-600 border-amber-100',
                    'draft' => 'bg-gray-50 text-gray-500 border-gray-100',
                    'cancelled' => 'bg-red-50 text-red-600 border-red-100',
                ];
                $billingColors = [
                    'pending' => 'bg-amber-50 text-amber-600 border-amber-100',
                    'billed' => 'bg-green-50 text-green-600 border-green-100',
                    'partially_billed' => 'bg-blue-50 text-blue-600 border-blue-100',
                    'cancelled' => 'bg-red-50 text-red-600 border-red-100',
                ];
            @endphp

            {{-- Main Info Card --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-theme-from to-theme-to px-8 py-5">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center">
                                <x-icon name="user" class="w-6 h-6 text-white" />
                            </div>
                            <div>
                                <h2 class="text-white text-lg font-bold">{{ $treatment->patient->full_name ?? '—' }}</h2>
                                <p class="text-sky-100 text-sm">Dr. {{ $treatment->doctor->user->name ?? '—' }}</p>
                            </div>
                        </div>
                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold border {{ $typeColors[$treatment->type] ?? $typeColors['other'] }}">
                            {{ ucfirst(str_replace('_', ' ', $treatment->type)) }}
                        </span>
                    </div>
                </div>

                <div class="px-8 py-6 grid grid-cols-2 md:grid-cols-3 gap-6">
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Status</p>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border {{ $statusColors[$treatment->status] ?? '' }}">
                            {{ ucfirst(str_replace('_', ' ', $treatment->status)) }}
                        </span>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Billing</p>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border {{ $billingColors[$treatment->billing_status] ?? '' }}">
                            {{ ucfirst(str_replace('_', ' ', $treatment->billing_status)) }}
                        </span>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Date</p>
                        <p class="font-semibold text-gray-800">{{ $treatment->created_at->format('M d, Y h:i A') }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Quantity</p>
                        <p class="font-semibold text-gray-800">{{ $treatment->quantity }}</p>
                    </div>
                    @if($treatment->service)
                        <div>
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Service</p>
                            <p class="font-semibold text-gray-800">{{ $treatment->service->name }}</p>
                        </div>
                    @endif
                    @if($treatment->pharmacyBatch)
                        <div>
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Medicine</p>
                            <p class="font-semibold text-gray-800">{{ $treatment->pharmacyBatch->pharmacyItem->commercial_name ?? '—' }}</p>
                            <p class="text-xs text-gray-400">Batch: {{ $treatment->pharmacyBatch->batch_number }}</p>
                        </div>
                    @endif
                    @if($treatment->description)
                        <div class="col-span-2 md:col-span-3">
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Description</p>
                            <p class="text-gray-700">{{ $treatment->description }}</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Pricing Card --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-8 py-5 border-b border-gray-100">
                    <h3 class="font-bold text-gray-800 flex items-center gap-2">
                        <x-icon name="currency" class="w-5 h-5 text-theme-from" />
                        Pricing Summary
                    </h3>
                </div>
                <div class="px-8 py-5 space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Unit Price</span>
                        <span class="font-semibold text-gray-800">${{ number_format($treatment->price, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Quantity</span>
                        <span class="font-semibold text-gray-800">× {{ $treatment->quantity }}</span>
                    </div>
                    @if($treatment->discount > 0)
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Discount</span>
                            <span class="font-semibold text-red-500">-${{ number_format($treatment->discount, 2) }}</span>
                        </div>
                    @endif
                    <div class="border-t border-dashed border-gray-200 pt-3 flex justify-between">
                        <span class="font-bold text-gray-800">Total</span>
                        <span class="text-xl font-extrabold text-theme-from">${{ number_format($treatment->total, 2) }}</span>
                    </div>
                </div>
            </div>

            {{-- Linked Invoices --}}
            @if($treatment->invoiceItems && $treatment->invoiceItems->count() > 0)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-8 py-5 border-b border-gray-100">
                        <h3 class="font-bold text-gray-800 flex items-center gap-2">
                            <x-icon name="document" class="w-5 h-5 text-theme-from" />
                            Linked Invoices
                        </h3>
                    </div>
                    <div class="px-8 py-4">
                        @foreach($treatment->invoiceItems as $item)
                            <div class="flex items-center justify-between py-2 {{ !$loop->last ? 'border-b border-gray-50' : '' }}">
                                <div>
                                    <a href="{{ route('invoices.show', $item->invoice->id) }}" class="font-semibold text-theme-from hover:underline">
                                        {{ $item->invoice->invoice_number }}
                                    </a>
                                    <p class="text-xs text-gray-400">{{ $item->invoice->created_at->format('M d, Y') }}</p>
                                </div>
                                <span class="font-bold text-gray-800">${{ number_format($item->total_price, 2) }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
