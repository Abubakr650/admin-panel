<x-app-layout>
@php $isArchived = request()->input('archived') === 'true'; @endphp
    <x-slot name="header">
        <div class="flex items-center justify-between w-full gap-4">
            {{-- Left: Title + Search --}}
            <div class="flex items-center gap-3 flex-1 min-w-0">
                <div class="shrink-0">
                    <p class="text-xs text-gray-400 uppercase tracking-widest font-semibold">Orthodontics</p>
                    <h1 class="text-lg font-bold text-gray-800 leading-tight">
                        {{ $isArchived ? 'Archived Cases' : 'Orthodontic Cases' }}
                    </h1>
                </div>

                <div class="h-8 w-px bg-gray-200 shrink-0"></div>

                <div class="w-64">
                    <x-search-bar
                        action="{{ route('orthodontics.cases.index') }}"
                        placeholder="Search patient name…"
                    />
                </div>
            </div>

            {{-- Right: Action Buttons --}}
            <div class="flex items-center gap-3 shrink-0">
                @unless($isArchived)
                    <x-button-link href="{{ route('orthodontics.cases.create') }}">
                        <x-icon name="plus" class="w-4 h-4" />
                        New Case
                    </x-button-link>
                @endunless

                <x-button-archive-view
                    :archived="$isArchived"
                    :indexRoute="route('orthodontics.cases.index')"
                    :archiveRoute="route('orthodontics.cases.index', ['archived' => 'true'])"
                    :archivedCount="$archivedCount ?? 0"
                />
            </div>
        </div>
    </x-slot>

    <div class="space-y-4">
        @if(!$isArchived)
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
            {{-- Total --}}
            <div class="relative overflow-hidden rounded-2xl bg-white border border-gray-100 shadow-sm p-4 flex items-center gap-4">
                <div class="flex-shrink-0 flex items-center justify-center w-11 h-11 rounded-xl bg-theme-from/10">
                    <x-icon name="users" class="w-5 h-5 text-theme-from" />
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total Cases</p>
                    <p class="text-2xl font-extrabold text-gray-800">{{ $cases->total() }}</p>
                </div>
                <div class="absolute -right-3 -bottom-3 w-16 h-16 rounded-full bg-theme-from/10 opacity-60"></div>
            </div>
            {{-- Active --}}
            <div class="relative overflow-hidden rounded-2xl bg-white border border-gray-100 shadow-sm p-4 flex items-center gap-4">
                <div class="flex-shrink-0 flex items-center justify-center w-11 h-11 rounded-xl bg-green-50">
                    <x-icon name="check-circle" class="w-5 h-5 text-green-500" />
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Active</p>
                    <p class="text-2xl font-extrabold text-gray-800">{{ $activeCount ?? '—' }}</p>
                </div>
                <div class="absolute -right-3 -bottom-3 w-16 h-16 rounded-full bg-green-50 opacity-60"></div>
            </div>
            {{-- Completed --}}
            <div class="relative overflow-hidden rounded-2xl bg-white border border-gray-100 shadow-sm p-4 flex items-center gap-4">
                <div class="flex-shrink-0 flex items-center justify-center w-11 h-11 rounded-xl bg-blue-50">
                    <x-icon name="badge-check" class="w-5 h-5 text-blue-500" />
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Completed</p>
                    <p class="text-2xl font-extrabold text-gray-800">{{ $completedCount ?? '—' }}</p>
                </div>
                <div class="absolute -right-3 -bottom-3 w-16 h-16 rounded-full bg-blue-50 opacity-60"></div>
            </div>
        </div>
        @endif

        <div class="rounded-2xl bg-white border border-gray-100 shadow-sm overflow-hidden">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-gray-50/70">
                        <th class="rounded-l-lg px-6 py-3 text-left text-md leading-4 font-semibold text-gray-500 uppercase tracking-wider">#</th>
                        <th class="px-6 py-3 text-left text-md leading-4 font-semibold text-gray-500 uppercase tracking-wider">Patient</th>
                        <th class="px-6 py-3 text-left text-md leading-4 font-semibold text-gray-500 uppercase tracking-wider">Doctor</th>
                        <th class="px-6 py-3 text-left text-md leading-4 font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-md leading-4 font-semibold text-gray-500 uppercase tracking-wider">Sessions</th>
                        @if($isArchived)
                            <th class="px-6 py-3 text-left text-md leading-4 font-semibold text-gray-500 uppercase tracking-wider">Archive Date</th>
                        @endif
                        <th class="rounded-r-lg px-6 py-3 text-right text-md leading-4 font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($cases as $case)
                        <tr class="group hover:bg-theme-from/10 transition-colors duration-150">
                            <td class="px-5 py-3.5 font-semibold text-gray-300">{{ $loop->iteration }}</td>
                            <td class="px-5 py-3.5">
                                @if($isArchived)
                                    <p class="font-semibold text-gray-700">{{ $case->patient->full_name ?? '—' }}</p>
                                @else
                                    <a href="{{ route('orthodontics.cases.show', $case->id) }}"
                                        class="font-semibold text-gray-800 hover:text-theme-from transition-colors duration-150">
                                        {{ $case->patient->full_name ?? '—' }}
                                    </a>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-gray-600">Dr. {{ $case->doctor->user->name ?? '—' }}</td>
                            <td class="px-5 py-3.5">
                                @if($case->status === 'active')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-green-50 text-green-600 border border-green-100">Active</span>
                                @elseif($case->status === 'completed')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-600 border border-blue-100">Completed</span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-50 text-gray-600 border border-gray-100">{{ ucfirst($case->status) }}</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="bg-theme-from/10 text-theme-from text-xs font-bold px-2.5 py-1 rounded-full">
                                    {{ $case->sessions_count }} Sessions
                                </span>
                            </td>
                            @if($isArchived)
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-1.5 text-gray-500">
                                        <x-icon name="calendar" class="w-3.5 h-3.5 text-amber-500" />
                                        {{ $case->deleted_at->format('M d, Y') }}
                                    </div>
                                </td>
                            @endif
                            <x-table-actions
                                :archived="$isArchived"
                                :editRoute="route('orthodontics.cases.edit', $case->id)"
                                :deleteRoute="route('orthodontics.cases.destroy', $case->id)"
                                :restoreRoute="route('orthodontics.cases.restore', $case->id)"
                                :itemName="'Case of ' . ($case->patient->full_name ?? 'Patient')"
                            />
                        </tr>
                    @empty
                        <x-message-empty colspan="{{ $isArchived ? 7 : 6 }}">
                            {{ $isArchived ? 'No archived cases found' : 'No orthodontic cases found' }}
                        </x-message-empty>
                    @endforelse
                </tbody>
            </table>

            @if($cases->hasPages())
                <div class="px-5 py-3 border-t border-gray-100 bg-gray-50/50">
                    {{ $cases->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
