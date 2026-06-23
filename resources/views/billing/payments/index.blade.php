<x-app-layout>
@php $isArchived = request()->input('archived') === 'true'; @endphp
    <x-slot name="header">
        <div class="flex items-center justify-between w-full gap-4">
            <div class="flex items-center gap-3 flex-1 min-w-0">
                <div class="shrink-0">
                    <p class="text-xs text-gray-400 uppercase tracking-widest font-semibold">Billing</p>
                    <h1 class="text-lg font-bold text-gray-800 leading-tight">{{ $isArchived ? 'Archived Payments' : 'Payments' }}</h1>
                </div>
                <div class="h-8 w-px bg-gray-200 shrink-0"></div>
                <div class="w-64">
                    <x-search-bar action="{{ route('payments.index') }}" placeholder="Search invoice or method…"
                        :extraParams="array_filter(['method' => request('method'), 'archived' => request('archived')])" />
                </div>
                @unless($isArchived)
                    <form action="{{ route('payments.index') }}" method="GET" class="flex items-center gap-2">
                        @if(request('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif
                        <select name="method" onchange="this.form.submit()"
                            class="pl-3 pr-8 py-2 text-sm font-semibold text-gray-700 bg-gray-50 border border-gray-200 rounded-xl appearance-none focus:outline-none focus:ring-2 focus:ring-theme-from/30 hover:bg-white transition-all cursor-pointer min-w-[130px]"
                            style="background-image: url('data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' fill=\'none\' viewBox=\'0 0 24 24\' stroke=\'%236b7280\'%3E%3Cpath stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M19 9l-7 7-7-7\'/%3E%3C/svg%3E'); background-position: right 8px center; background-size: 14px; background-repeat: no-repeat;">
                            <option value="all">All Methods</option>
                            <option value="cash" {{ request('method') === 'cash' ? 'selected' : '' }}>💵 Cash</option>
                            <option value="card" {{ request('method') === 'card' ? 'selected' : '' }}>💳 Card</option>
                            <option value="bank_transfer" {{ request('method') === 'bank_transfer' ? 'selected' : '' }}>🏦 Transfer</option>
                        </select>
                    </form>
                @endunless
            </div>
            <div class="flex items-center gap-3 shrink-0">
                @unless($isArchived)
                    <div class="px-4 py-2 bg-green-50 border border-green-100 rounded-xl">
                        <span class="text-xs text-green-600 font-semibold">Total Collected</span>
                        <span class="ml-2 text-sm font-extrabold text-green-700">${{ number_format($totalCollected ?? 0, 2) }}</span>
                    </div>
                    <x-button-link href="{{ route('payments.create') }}">
                        <x-icon name="plus" class="w-4 h-4" /> New Payment
                    </x-button-link>
                @endunless
                <x-button-archive-view
                    :archived="$isArchived"
                    :indexRoute="route('payments.index')"
                    :archiveRoute="route('payments.index', ['archived' => 'true'])"
                    :archivedCount="$archivedCount ?? 0"
                />
            </div>
        </div>
    </x-slot>

    <div class="space-y-4">
        <div class="rounded-2xl bg-white border border-gray-100 shadow-sm overflow-hidden">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-gray-50/70">
                        <th class="px-6 py-3 text-left text-md leading-4 font-semibold text-gray-500 uppercase tracking-wider">#</th>
                        <th class="px-6 py-3 text-left text-md leading-4 font-semibold text-gray-500 uppercase tracking-wider">Invoice</th>
                        <th class="px-6 py-3 text-left text-md leading-4 font-semibold text-gray-500 uppercase tracking-wider">Patient</th>
                        <th class="px-6 py-3 text-right text-md leading-4 font-semibold text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-md leading-4 font-semibold text-gray-500 uppercase tracking-wider">Method</th>
                        <th class="px-6 py-3 text-left text-md leading-4 font-semibold text-gray-500 uppercase tracking-wider">Paid At</th>
                        @if($isArchived)
                            <th class="px-6 py-3 text-left text-md leading-4 font-semibold text-gray-500 uppercase tracking-wider">Archived</th>
                        @endif
                        <th class="px-6 py-3 text-right text-md leading-4 font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($payments as $payment)
                        @php
                            $methodIcons = ['cash' => '💵', 'card' => '💳', 'bank_transfer' => '🏦', 'other' => '💱'];
                        @endphp
                        <tr class="group hover:bg-theme-from/10 transition-colors duration-150">
                            <td class="px-5 py-3.5 font-semibold text-gray-300">{{ $loop->iteration }}</td>
                            <td class="px-5 py-3.5">
                                <a href="{{ route('invoices.show', $payment->invoice_id) }}"
                                   class="font-bold text-theme-from hover:underline">
                                    {{ $payment->invoice->invoice_number ?? '—' }}
                                </a>
                            </td>
                            <td class="px-5 py-3.5 text-gray-700">{{ $payment->invoice->patient->full_name ?? '—' }}</td>
                            <td class="px-5 py-3.5 text-right font-extrabold text-green-600">${{ number_format($payment->amount, 2) }}</td>
                            <td class="px-5 py-3.5 text-gray-600">
                                {{ $methodIcons[$payment->payment_method] ?? '💱' }}
                                {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}
                            </td>
                            <td class="px-5 py-3.5 text-gray-500 text-sm">
                                {{ $payment->paid_at?->format('M d, Y') ?? '—' }}
                            </td>
                            @if($isArchived)
                                <td class="px-5 py-3.5 text-gray-500 text-sm">{{ $payment->deleted_at->format('M d, Y') }}</td>
                            @endif
                            <x-table-actions
                                :archived="$isArchived"
                                :editRoute="route('payments.edit', $payment->id)"
                                :deleteRoute="route('payments.destroy', $payment->id)"
                                :restoreRoute="route('payments.restore', $payment->id)"
                                :itemName="'payment of $' . number_format($payment->amount, 2)"
                            />
                        </tr>
                    @empty
                        <x-message-empty colspan="{{ $isArchived ? 8 : 7 }}">
                            {{ $isArchived ? 'No archived payments' : 'No payments found' }}
                        </x-message-empty>
                    @endforelse
                </tbody>
            </table>
            @if($payments->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">{{ $payments->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>
