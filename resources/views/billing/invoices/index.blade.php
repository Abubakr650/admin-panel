<x-app-layout>
@php $isArchived = request()->input('archived') === 'true'; @endphp
    <x-slot name="header">
        <div class="flex items-center justify-between w-full gap-4">
            <div class="flex items-center gap-3 flex-1 min-w-0">
                <div class="shrink-0">
                    <p class="text-xs text-gray-400 uppercase tracking-widest font-semibold">Billing</p>
                    <h1 class="text-lg font-bold text-gray-800 leading-tight">{{ $isArchived ? 'Archived Invoices' : 'Invoices' }}</h1>
                </div>
                <div class="h-8 w-px bg-gray-200 shrink-0"></div>
                <div class="w-64">
                    <x-search-bar
                        action="{{ route('invoices.index') }}"
                        placeholder="Search by invoice # or patient…"
                        :extraParams="array_filter(['payment_status' => request('payment_status'), 'archived' => request('archived')])"
                    />
                </div>
                @unless($isArchived)
                    <form action="{{ route('invoices.index') }}" method="GET" class="flex items-center gap-2">
                        @if(request('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif
                        <select name="payment_status" onchange="this.form.submit()"
                            class="pl-3 pr-8 py-2 text-sm font-semibold text-gray-700 bg-gray-50 border border-gray-200 rounded-xl appearance-none focus:outline-none focus:ring-2 focus:ring-theme-from/30 hover:bg-white transition-all cursor-pointer min-w-[130px]"
                            style="background-image: url('data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' fill=\'none\' viewBox=\'0 0 24 24\' stroke=\'%236b7280\'%3E%3Cpath stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M19 9l-7 7-7-7\'/%3E%3C/svg%3E'); background-position: right 8px center; background-size: 14px; background-repeat: no-repeat;">
                            <option value="all">All Status</option>
                            <option value="unpaid" {{ request('payment_status') === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                            <option value="partial" {{ request('payment_status') === 'partial' ? 'selected' : '' }}>Partial</option>
                            <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>Paid</option>
                        </select>
                    </form>
                @endunless
            </div>
            <div class="flex items-center gap-3 shrink-0">
                @unless($isArchived)
                    <x-button-link href="{{ route('invoices.create') }}">
                        <x-icon name="plus" class="w-4 h-4" /> New Invoice
                    </x-button-link>
                @endunless
                <x-button-archive-view
                    :archived="$isArchived"
                    :indexRoute="route('invoices.index')"
                    :archiveRoute="route('invoices.index', ['archived' => 'true'])"
                    :archivedCount="$archivedCount ?? 0"
                />
            </div>
        </div>
    </x-slot>

    <div class="space-y-4">
        @if(!$isArchived)
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="relative overflow-hidden rounded-2xl bg-white border border-gray-100 shadow-sm p-4 flex items-center gap-4">
                <div class="flex-shrink-0 flex items-center justify-center w-11 h-11 rounded-xl bg-theme-from/10">
                    <x-icon name="document" class="w-5 h-5 text-theme-from" />
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total Invoices</p>
                    <p class="text-2xl font-extrabold text-gray-800">{{ $invoices->total() ?? 0 }}</p>
                </div>
                <div class="absolute -right-3 -bottom-3 w-16 h-16 rounded-full bg-theme-from/10 opacity-60"></div>
            </div>
            <div class="relative overflow-hidden rounded-2xl bg-white border border-gray-100 shadow-sm p-4 flex items-center gap-4">
                <div class="flex-shrink-0 flex items-center justify-center w-11 h-11 rounded-xl bg-green-50">
                    <x-icon name="check-circle" class="w-5 h-5 text-green-500" />
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Paid</p>
                    <p class="text-2xl font-extrabold text-gray-800">{{ $paidCount ?? 0 }}</p>
                </div>
                <div class="absolute -right-3 -bottom-3 w-16 h-16 rounded-full bg-green-50 opacity-60"></div>
            </div>
            <div class="relative overflow-hidden rounded-2xl bg-white border border-gray-100 shadow-sm p-4 flex items-center gap-4">
                <div class="flex-shrink-0 flex items-center justify-center w-11 h-11 rounded-xl bg-amber-50">
                    <x-icon name="clock" class="w-5 h-5 text-amber-500" />
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Partial</p>
                    <p class="text-2xl font-extrabold text-gray-800">{{ $partialCount ?? 0 }}</p>
                </div>
                <div class="absolute -right-3 -bottom-3 w-16 h-16 rounded-full bg-amber-50 opacity-60"></div>
            </div>
            <div class="relative overflow-hidden rounded-2xl bg-white border border-gray-100 shadow-sm p-4 flex items-center gap-4">
                <div class="flex-shrink-0 flex items-center justify-center w-11 h-11 rounded-xl bg-red-50">
                    <x-icon name="x" class="w-5 h-5 text-red-500" />
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Unpaid</p>
                    <p class="text-2xl font-extrabold text-gray-800">{{ $unpaidCount ?? 0 }}</p>
                </div>
                <div class="absolute -right-3 -bottom-3 w-16 h-16 rounded-full bg-red-50 opacity-60"></div>
            </div>
        </div>
        @endif

        <div class="rounded-2xl bg-white border border-gray-100 shadow-sm overflow-hidden">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-gray-50/70">
                        <th class="px-6 py-3 text-left text-md leading-4 font-semibold text-gray-500 uppercase tracking-wider">#</th>
                        <th class="px-6 py-3 text-left text-md leading-4 font-semibold text-gray-500 uppercase tracking-wider">Invoice #</th>
                        <th class="px-6 py-3 text-left text-md leading-4 font-semibold text-gray-500 uppercase tracking-wider">Patient</th>
                        <th class="px-6 py-3 text-left text-md leading-4 font-semibold text-gray-500 uppercase tracking-wider">Doctor</th>
                        <th class="px-6 py-3 text-right text-md leading-4 font-semibold text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-md leading-4 font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-md leading-4 font-semibold text-gray-500 uppercase tracking-wider">Date</th>
                        @if($isArchived)
                            <th class="px-6 py-3 text-left text-md leading-4 font-semibold text-gray-500 uppercase tracking-wider">Archived</th>
                        @endif
                        <th class="px-6 py-3 text-right text-md leading-4 font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($invoices as $invoice)
                        @php
                            $statusColors = [
                                'unpaid'  => 'bg-red-50 text-red-600 border-red-100',
                                'partial' => 'bg-amber-50 text-amber-600 border-amber-100',
                                'paid'    => 'bg-green-50 text-green-600 border-green-100',
                            ];
                        @endphp
                        <tr class="group hover:bg-theme-from/10 transition-colors duration-150">
                            <td class="px-5 py-3.5 font-semibold text-gray-300">{{ $loop->iteration }}</td>
                            <td class="px-5 py-3.5">
                                <a href="{{ route('invoices.show', $invoice->id) }}" class="font-bold text-theme-from hover:underline">
                                    {{ $invoice->invoice_number }}
                                </a>
                            </td>
                            <td class="px-5 py-3.5 font-semibold text-gray-800">{{ $invoice->patient->full_name ?? '—' }}</td>
                            <td class="px-5 py-3.5 text-gray-600">Dr. {{ $invoice->doctor->user->name ?? '—' }}</td>
                            <td class="px-5 py-3.5 text-right font-bold text-gray-800">${{ number_format($invoice->final_amount, 2) }}</td>
                            <td class="px-5 py-3.5">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border {{ $statusColors[$invoice->payment_status] ?? 'bg-gray-50 text-gray-600 border-gray-100' }}">
                                    {{ ucfirst($invoice->payment_status) }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-gray-500 text-sm">{{ $invoice->created_at->format('M d, Y') }}</td>
                            @if($isArchived)
                                <td class="px-5 py-3.5 text-gray-500 text-sm">{{ $invoice->deleted_at->format('M d, Y') }}</td>
                            @endif
                            <x-table-actions
                                :archived="$isArchived"
                                :editRoute="route('invoices.edit', $invoice->id)"
                                :deleteRoute="route('invoices.destroy', $invoice->id)"
                                :restoreRoute="route('invoices.restore', $invoice->id)"
                                :itemName="'Invoice ' . $invoice->invoice_number"
                            />
                        </tr>
                    @empty
                        <x-message-empty colspan="{{ $isArchived ? 9 : 8 }}">
                            {{ $isArchived ? 'No archived invoices' : 'No invoices found' }}
                        </x-message-empty>
                    @endforelse
                </tbody>
            </table>
            @if($invoices->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">{{ $invoices->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>
