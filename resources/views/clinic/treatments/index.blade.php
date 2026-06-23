<x-app-layout>
@php $isArchived = request()->input('archived') === 'true'; @endphp
    <x-slot name="header">
        <div class="flex items-center justify-between w-full gap-4">
            {{-- Left: Title + Search + Filters --}}
            <div class="flex items-center gap-3 flex-1 min-w-0">
                <div class="shrink-0">
                    <p class="text-xs text-gray-400 uppercase tracking-widest font-semibold">Clinic</p>
                    <h1 class="text-lg font-bold text-gray-800 leading-tight">
                        {{ $isArchived ? 'Archived Treatments' : 'Treatments' }}
                    </h1>
                </div>
                <div class="h-8 w-px bg-gray-200 shrink-0"></div>
                <div class="w-64">
                    <x-search-bar
                        action="{{ route('treatments.index') }}"
                        placeholder="Search by patient or doctor…"
                        :extraParams="array_filter([
                            'type' => request('type'),
                            'status' => request('status'),
                            'billing_status' => request('billing_status'),
                            'archived' => request('archived')
                        ])"
                    />
                </div>

                @unless($isArchived)
                    <form action="{{ route('treatments.index') }}" method="GET" class="flex items-center gap-2">
                        @if(request('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif

                        {{-- Type Filter --}}
                        <select name="type" onchange="this.form.submit()"
                                class="pl-3 pr-8 py-2 text-sm font-semibold text-gray-700 bg-gray-50 border border-gray-200 rounded-xl appearance-none focus:outline-none focus:ring-2 focus:ring-theme-from/30 hover:bg-white transition-all cursor-pointer min-w-[130px]"
                                style="background-image: url('data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' fill=\'none\' viewBox=\'0 0 24 24\' stroke=\'%236b7280\'%3E%3Cpath stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M19 9l-7 7-7-7\'/%3E%3C/svg%3E'); background-position: right 8px center; background-size: 14px; background-repeat: no-repeat;">
                            <option value="all">All Types</option>
                            <option value="consultation" {{ request('type') === 'consultation' ? 'selected' : '' }}>Consultation</option>
                            <option value="filling" {{ request('type') === 'filling' ? 'selected' : '' }}>Filling</option>
                            <option value="extraction" {{ request('type') === 'extraction' ? 'selected' : '' }}>Extraction</option>
                            <option value="cleaning" {{ request('type') === 'cleaning' ? 'selected' : '' }}>Cleaning</option>
                            <option value="cosmetic" {{ request('type') === 'cosmetic' ? 'selected' : '' }}>Cosmetic</option>
                            <option value="radiology" {{ request('type') === 'radiology' ? 'selected' : '' }}>Radiology</option>
                            <option value="orthodontic_session" {{ request('type') === 'orthodontic_session' ? 'selected' : '' }}>Orthodontic</option>
                            <option value="pharmacy_dispense" {{ request('type') === 'pharmacy_dispense' ? 'selected' : '' }}>Pharmacy</option>
                            <option value="other" {{ request('type') === 'other' ? 'selected' : '' }}>Other</option>
                        </select>

                        {{-- Status Filter --}}
                        <select name="status" onchange="this.form.submit()"
                                class="pl-3 pr-8 py-2 text-sm font-semibold text-gray-700 bg-gray-50 border border-gray-200 rounded-xl appearance-none focus:outline-none focus:ring-2 focus:ring-theme-from/30 hover:bg-white transition-all cursor-pointer min-w-[130px]"
                                style="background-image: url('data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' fill=\'none\' viewBox=\'0 0 24 24\' stroke=\'%236b7280\'%3E%3Cpath stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M19 9l-7 7-7-7\'/%3E%3C/svg%3E'); background-position: right 8px center; background-size: 14px; background-repeat: no-repeat;">
                            <option value="all">All Status</option>
                            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="planned" {{ request('status') === 'planned' ? 'selected' : '' }}>Planned</option>
                            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>

                        {{-- Billing Filter --}}
                        <select name="billing_status" onchange="this.form.submit()"
                                class="pl-3 pr-8 py-2 text-sm font-semibold text-gray-700 bg-gray-50 border border-gray-200 rounded-xl appearance-none focus:outline-none focus:ring-2 focus:ring-theme-from/30 hover:bg-white transition-all cursor-pointer min-w-[130px]"
                                style="background-image: url('data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' fill=\'none\' viewBox=\'0 0 24 24\' stroke=\'%236b7280\'%3E%3Cpath stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M19 9l-7 7-7-7\'/%3E%3C/svg%3E'); background-position: right 8px center; background-size: 14px; background-repeat: no-repeat;">
                            <option value="all">All Billing</option>
                            <option value="pending" {{ request('billing_status') === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="billed" {{ request('billing_status') === 'billed' ? 'selected' : '' }}>Billed</option>
                            <option value="partially_billed" {{ request('billing_status') === 'partially_billed' ? 'selected' : '' }}>Partial</option>
                        </select>

                        @if(request('type') || request('status') || request('billing_status'))
                            <a href="{{ route('treatments.index') }}"
                               class="inline-flex items-center justify-center w-9 h-9 bg-gray-100 hover:bg-red-50 text-gray-400 hover:text-red-500 rounded-xl transition-all duration-200"
                               title="Clear Filters">
                                <x-icon name="x" class="w-4 h-4" />
                            </a>
                        @endif
                    </form>
                @endunless
            </div>

            {{-- Right: Actions --}}
            <div class="flex items-center gap-3 shrink-0">
                @unless($isArchived)
                    <x-button-link href="{{ route('treatments.create') }}">
                        <x-icon name="plus" class="w-4 h-4" />
                        New Treatment
                    </x-button-link>
                @endunless
                <x-button-archive-view
                    :archived="$isArchived"
                    :indexRoute="route('treatments.index')"
                    :archiveRoute="route('treatments.index', ['archived' => 'true'])"
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
                    <x-icon name="clipboard" class="w-5 h-5 text-theme-from" />
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total</p>
                    <p class="text-2xl font-extrabold text-gray-800">{{ $totalCount ?? 0 }}</p>
                </div>
                <div class="absolute -right-3 -bottom-3 w-16 h-16 rounded-full bg-theme-from/10 opacity-60"></div>
            </div>
            <div class="relative overflow-hidden rounded-2xl bg-white border border-gray-100 shadow-sm p-4 flex items-center gap-4">
                <div class="flex-shrink-0 flex items-center justify-center w-11 h-11 rounded-xl bg-green-50">
                    <x-icon name="check-circle" class="w-5 h-5 text-green-500" />
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Completed</p>
                    <p class="text-2xl font-extrabold text-gray-800">{{ $completedCount ?? 0 }}</p>
                </div>
                <div class="absolute -right-3 -bottom-3 w-16 h-16 rounded-full bg-green-50 opacity-60"></div>
            </div>
            <div class="relative overflow-hidden rounded-2xl bg-white border border-gray-100 shadow-sm p-4 flex items-center gap-4">
                <div class="flex-shrink-0 flex items-center justify-center w-11 h-11 rounded-xl bg-amber-50">
                    <x-icon name="clock" class="w-5 h-5 text-amber-500" />
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Pending Billing</p>
                    <p class="text-2xl font-extrabold text-gray-800">{{ $pendingBillingCount ?? 0 }}</p>
                </div>
                <div class="absolute -right-3 -bottom-3 w-16 h-16 rounded-full bg-amber-50 opacity-60"></div>
            </div>
            <div class="relative overflow-hidden rounded-2xl bg-white border border-gray-100 shadow-sm p-4 flex items-center gap-4">
                <div class="flex-shrink-0 flex items-center justify-center w-11 h-11 rounded-xl bg-blue-50">
                    <x-icon name="archive" class="w-5 h-5 text-blue-500" />
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Archived</p>
                    <p class="text-2xl font-extrabold text-gray-800">{{ $archivedCount ?? 0 }}</p>
                </div>
                <div class="absolute -right-3 -bottom-3 w-16 h-16 rounded-full bg-blue-50 opacity-60"></div>
            </div>
        </div>
        @endif

        <div class="rounded-2xl bg-white border border-gray-100 shadow-sm overflow-hidden">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-gray-50/70">
                        <th class="px-6 py-3 text-left text-md leading-4 font-semibold text-gray-500 uppercase tracking-wider">#</th>
                        <th class="px-6 py-3 text-left text-md leading-4 font-semibold text-gray-500 uppercase tracking-wider">Patient</th>
                        <th class="px-6 py-3 text-left text-md leading-4 font-semibold text-gray-500 uppercase tracking-wider">Doctor</th>
                        <th class="px-6 py-3 text-left text-md leading-4 font-semibold text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-md leading-4 font-semibold text-gray-500 uppercase tracking-wider">Description</th>
                        <th class="px-6 py-3 text-right text-md leading-4 font-semibold text-gray-500 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-3 text-left text-md leading-4 font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-md leading-4 font-semibold text-gray-500 uppercase tracking-wider">Billing</th>
                        @if($isArchived)
                            <th class="px-6 py-3 text-left text-md leading-4 font-semibold text-gray-500 uppercase tracking-wider">Archive Date</th>
                        @endif
                        <th class="px-6 py-3 text-right text-md leading-4 font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($treatments as $treatment)
                        @php
                            $typeColors = [
                                'consultation'       => 'bg-indigo-50 text-indigo-600 border-indigo-100',
                                'filling'            => 'bg-blue-50 text-blue-600 border-blue-100',
                                'extraction'         => 'bg-red-50 text-red-600 border-red-100',
                                'cleaning'           => 'bg-teal-50 text-teal-600 border-teal-100',
                                'cosmetic'           => 'bg-pink-50 text-pink-600 border-pink-100',
                                'radiology'          => 'bg-purple-50 text-purple-600 border-purple-100',
                                'orthodontic_session' => 'bg-cyan-50 text-cyan-600 border-cyan-100',
                                'pharmacy_dispense'  => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                                'other'              => 'bg-gray-50 text-gray-600 border-gray-100',
                            ];
                            $statusColors = [
                                'completed'   => 'bg-green-50 text-green-600 border-green-100',
                                'in_progress' => 'bg-blue-50 text-blue-600 border-blue-100',
                                'planned'     => 'bg-amber-50 text-amber-600 border-amber-100',
                                'draft'       => 'bg-gray-50 text-gray-500 border-gray-100',
                                'cancelled'   => 'bg-red-50 text-red-600 border-red-100',
                            ];
                            $billingColors = [
                                'pending'          => 'bg-amber-50 text-amber-600 border-amber-100',
                                'billed'           => 'bg-green-50 text-green-600 border-green-100',
                                'partially_billed' => 'bg-blue-50 text-blue-600 border-blue-100',
                                'cancelled'        => 'bg-red-50 text-red-600 border-red-100',
                            ];
                        @endphp
                        <tr class="group hover:bg-theme-from/10 transition-colors duration-150">
                            <td class="px-5 py-3.5 font-semibold text-gray-300">{{ $loop->iteration }}</td>
                            <td class="px-5 py-3.5">
                                <a href="{{ route('treatments.show', $treatment->id) }}" class="font-semibold text-gray-800 hover:text-theme-from transition-colors">
                                    {{ $treatment->patient->full_name ?? '—' }}
                                </a>
                            </td>
                            <td class="px-5 py-3.5 text-gray-600 font-medium">
                                {{ $treatment->doctor->user->name ?? '—' }}
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border {{ $typeColors[$treatment->type] ?? $typeColors['other'] }}">
                                    {{ ucfirst(str_replace('_', ' ', $treatment->type)) }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-gray-500 text-sm max-w-[200px] truncate">
                                {{ $treatment->description ?? '—' }}
                            </td>
                            <td class="px-5 py-3.5 text-right font-bold text-gray-800">
                                ${{ number_format($treatment->total, 2) }}
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border {{ $statusColors[$treatment->status] ?? 'bg-gray-50 text-gray-600 border-gray-100' }}">
                                    {{ ucfirst(str_replace('_', ' ', $treatment->status)) }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border {{ $billingColors[$treatment->billing_status] ?? 'bg-gray-50 text-gray-600 border-gray-100' }}">
                                    {{ ucfirst(str_replace('_', ' ', $treatment->billing_status)) }}
                                </span>
                            </td>
                            @if($isArchived)
                                <td class="px-5 py-3.5 text-gray-500 text-sm">
                                    {{ $treatment->deleted_at->format('M d, Y') }}
                                </td>
                            @endif
                            <x-table-actions
                                :archived="$isArchived"
                                :editRoute="route('treatments.edit', $treatment->id)"
                                :deleteRoute="route('treatments.destroy', $treatment->id)"
                                :restoreRoute="route('treatments.restore', $treatment->id)"
                                :itemName="'Treatment for ' . ($treatment->patient->full_name ?? 'N/A')"
                            />
                        </tr>
                    @empty
                        <x-message-empty colspan="{{ $isArchived ? 10 : 9 }}">
                            {{ $isArchived ? 'No archived treatments found' : 'No treatments found' }}
                        </x-message-empty>
                    @endforelse
                </tbody>
            </table>

            @if($treatments->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
                    {{ $treatments->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
