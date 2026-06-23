<x-app-layout>
@php $isArchived = request()->input('archived') === 'true'; @endphp
    <x-slot name="header">
        <div class="flex items-center justify-between w-full gap-4">
            {{-- Left: Title + Search --}}
            <div class="flex items-center gap-3 flex-1 min-w-0">
                <div class="shrink-0">
                    <p class="text-xs text-gray-400 uppercase tracking-widest font-semibold">Orthodontics</p>
                    <h1 class="text-lg font-bold text-gray-800 leading-tight">
                        {{ $isArchived ? 'Archived Sessions' : 'Orthodontic Sessions' }}
                    </h1>
                </div>

                <div class="h-8 w-px bg-gray-200 shrink-0"></div>

                <div class="w-64">
                    <x-search-bar
                        action="{{ route('orthodontics.sessions.index') }}"
                        placeholder="Search patient name…"
                    />
                </div>
            </div>

            {{-- Right: Action Buttons --}}
            <div class="flex items-center gap-3 shrink-0">
                @unless($isArchived)
                    <x-button-link href="{{ route('orthodontics.sessions.create') }}">
                        <x-icon name="plus" class="w-4 h-4" />
                        Log Session
                    </x-button-link>
                @endunless

                <x-button-archive-view
                    :archived="$isArchived"
                    :indexRoute="route('orthodontics.sessions.index')"
                    :archiveRoute="route('orthodontics.sessions.index', ['archived' => 'true'])"
                    :archivedCount="$archivedCount ?? 0"
                />
            </div>
        </div>
    </x-slot>

    <div class="space-y-4">
        @if(!$isArchived)
        <div class="grid grid-cols-1 sm:grid-cols-1 gap-4">
            {{-- Total --}}
            <div class="relative overflow-hidden rounded-2xl bg-white border border-gray-100 shadow-sm p-4 flex items-center gap-4">
                <div class="flex-shrink-0 flex items-center justify-center w-11 h-11 rounded-xl bg-theme-from/10">
                    <x-icon name="clipboard-list" class="w-5 h-5 text-theme-from" />
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total Sessions</p>
                    <p class="text-2xl font-extrabold text-gray-800">{{ $sessions->total() }}</p>
                </div>
                <div class="absolute -right-3 -bottom-3 w-16 h-16 rounded-full bg-theme-from/10 opacity-60"></div>
            </div>
        </div>
        @endif

        <div class="rounded-2xl bg-white border border-gray-100 shadow-sm overflow-hidden">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-gray-50/70">
                        <th class="rounded-l-lg px-6 py-3 text-left text-md leading-4 font-semibold text-gray-500 uppercase tracking-wider">#</th>
                        <th class="px-6 py-3 text-left text-md leading-4 font-semibold text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-md leading-4 font-semibold text-gray-500 uppercase tracking-wider">Patient</th>
                        <th class="px-6 py-3 text-left text-md leading-4 font-semibold text-gray-500 uppercase tracking-wider">Treatment</th>
                        @if($isArchived)
                            <th class="px-6 py-3 text-left text-md leading-4 font-semibold text-gray-500 uppercase tracking-wider">Archive Date</th>
                        @endif
                        <th class="rounded-r-lg px-6 py-3 text-right text-md leading-4 font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($sessions as $session)
                        <tr class="group hover:bg-theme-from/10 transition-colors duration-150">
                            <td class="px-5 py-3.5 font-semibold text-gray-300">{{ $loop->iteration }}</td>
                            <td class="px-5 py-3.5 text-gray-600">{{ $session->session_date }}</td>
                            <td class="px-5 py-3.5">
                                @if($isArchived)
                                    <p class="font-semibold text-gray-700">{{ $session->orthodonticCase->patient->full_name ?? '—' }}</p>
                                @else
                                    <a href="{{ route('orthodontics.cases.show', $session->case_id) }}"
                                        class="font-semibold text-gray-800 hover:text-theme-from transition-colors duration-150">
                                        {{ $session->orthodonticCase->patient->full_name ?? '—' }}
                                    </a>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-gray-600">{{ Str::limit($session->treatment, 50) }}</td>
                            @if($isArchived)
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-1.5 text-gray-500">
                                        <x-icon name="calendar" class="w-3.5 h-3.5 text-amber-500" />
                                        {{ $session->deleted_at->format('M d, Y') }}
                                    </div>
                                </td>
                            @endif
                            <x-table-actions
                                :archived="$isArchived"
                                :editRoute="route('orthodontics.sessions.edit', $session->id)"
                                :deleteRoute="route('orthodontics.sessions.destroy', $session->id)"
                                :restoreRoute="route('orthodontics.sessions.restore', $session->id)"
                                :itemName="'Session on ' . $session->session_date"
                                :showRoute="route('orthodontics.sessions.show', $session->id)"
                            />
                        </tr>
                    @empty
                        <x-message-empty colspan="{{ $isArchived ? 6 : 5 }}">
                            {{ $isArchived ? 'No archived sessions found' : 'No orthodontic sessions found' }}
                        </x-message-empty>
                    @endforelse
                </tbody>
            </table>

            @if($sessions->hasPages())
                <div class="px-5 py-3 border-t border-gray-100 bg-gray-50/50">
                    {{ $sessions->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
