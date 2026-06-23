<x-app-layout>
@php $isArchived = request()->input('archived') === 'true'; @endphp
    <x-slot name="header">
        <div class="flex items-center justify-between w-full gap-4">
            {{-- Left: Title + Search + Status Filter --}}
            <div class="flex items-center gap-3 flex-1 min-w-0">
                {{-- Page Title --}}
                <div class="shrink-0">
                    <p class="text-xs text-gray-400 uppercase tracking-widest font-semibold">Clinic</p>
                    <h1 class="text-lg font-bold text-gray-800 leading-tight">
                        {{ $isArchived ? 'Archived Doctors' : 'Doctors' }}
                    </h1>
                </div>

                {{-- Divider --}}
                <div class="h-8 w-px bg-gray-200 shrink-0"></div>

                {{-- Search Bar --}}
                <div class="w-64">
                    <x-search-bar
                        action="{{ route('doctors.index') }}"
                        placeholder="Search by name, specialty…"
                        :extraParams="array_filter(['status' => request()->input('status')])"
                    />
                </div>

                {{-- Status Filter --}}
                @unless($isArchived)
                <div class="flex items-center gap-1 bg-gray-100 rounded-xl p-1 shrink-0">
                    @foreach(['all' => 'All', 'active' => 'Active', 'inactive' => 'Inactive'] as $value => $label)
                        @php
                            $activeStatus = request()->input('status', 'all');
                            $isActiveTab  = $activeStatus === $value;
                            $href = route('doctors.index', array_filter([
                                'search' => request()->input('search'),
                                'status' => $value === 'all' ? null : $value,
                            ]));
                        @endphp
                        <a href="{{ $href }}"
                           class="px-3 py-1 rounded-lg text-sm font-semibold transition-all duration-150
                                  {{ $isActiveTab
                                      ? 'bg-white text-gray-800 shadow-sm'
                                      : 'text-gray-500 hover:text-gray-700' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>
                @endunless
            </div>

            {{-- Right: Action Buttons --}}
            <div class="flex items-center gap-3 shrink-0">
                @unless($isArchived)
                    <x-button-link href="{{ route('doctors.create') }}">
                        <x-icon name="plus" class="w-4 h-4" />
                        New Doctor
                    </x-button-link>
                @endunless

                <x-button-archive-view
                    :archived="$isArchived"
                    :indexRoute="route('doctors.index')"
                    :archiveRoute="route('doctors.index', ['archived' => 'true'])"
                    :archivedCount="$archivedCount ?? 0"
                />
            </div>
        </div>
    </x-slot>

    {{-- Page Body --}}
    <div class="space-y-4">

        {{-- Toast --}}
        @if(!$isArchived)
        {{-- ── Stats Row ──────────────────────────────────────────────── --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
            {{-- Total --}}
            <div class="relative overflow-hidden rounded-2xl bg-white border border-gray-100 shadow-sm p-4 flex items-center gap-4">
                <div class="flex-shrink-0 flex items-center justify-center w-11 h-11 rounded-xl bg-theme-from/10">
                    <x-icon name="users" class="w-5 h-5 text-theme-from" />
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total</p>
                    <p class="text-2xl font-extrabold text-gray-800">{{ $doctors->total() ?? '—'  }}</p>
                </div>
                <div class="absolute -right-3 -bottom-3 w-16 h-16 rounded-full bg-theme-from/10 opacity-60"></div>
            </div>
            {{-- Active --}}
            <div class="relative overflow-hidden rounded-2xl bg-white border border-gray-100 shadow-sm p-4 flex items-center gap-4">
                <div class="flex-shrink-0 flex items-center justify-center w-11 h-11 rounded-xl bg-emerald-50">
                    <x-icon name="user" class="w-5 h-5 text-emerald-500" />
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Active</p>
                    <p class="text-2xl font-extrabold text-gray-800">{{ $activeCount ?? '—' }}</p>
                </div>
                <div class="absolute -right-3 -bottom-3 w-16 h-16 rounded-full bg-emerald-50 opacity-60"></div>
            </div>
            {{-- Inactive --}}
            <div class="relative overflow-hidden rounded-2xl bg-white border border-gray-100 shadow-sm p-4 flex items-center gap-4">
                <div class="flex-shrink-0 flex items-center justify-center w-11 h-11 rounded-xl bg-red-50">
                    <x-icon name="user" class="w-5 h-5 text-red-400" />
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Inactive</p>
                    <p class="text-2xl font-extrabold text-gray-800">{{ $inactiveCount ?? '—' }}</p>
                </div>
                <div class="absolute -right-3 -bottom-3 w-16 h-16 rounded-full bg-red-50 opacity-60"></div>
            </div>
        </div>
        @endif

        {{-- ── Table Card ─────────────────────────────────────────────── --}}
        <div class="rounded-2xl bg-white border border-gray-100 shadow-sm overflow-hidden">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-gray-50/70">
                        <th class="px-6 py-3 text-left text-md leading-4 font-semibold text-gray-500 uppercase tracking-wider">#</th>
                        <th class="px-6 py-3 text-left text-md leading-4 font-semibold text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-md leading-4 font-semibold text-gray-500 uppercase tracking-wider">Specialty</th>
                        <th class="px-6 py-3 text-left text-md leading-4 font-semibold text-gray-500 uppercase tracking-wider">Degree</th>
                        <th class="px-6 py-3 text-left text-md leading-4 font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        @if($isArchived)
                            <th class="px-6 py-3 text-left text-md leading-4 font-semibold text-gray-500 uppercase tracking-wider">Archive Date</th>
                        @endif
                        <th class="px-6 py-3 bg-gray-50 text-right text-md leading-4 font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($doctors as $doctor)
                        <tr class="group hover:bg-theme-from/10 transition-colors duration-150">
                            {{-- Row number --}}
                            <td class="px-5 py-3.5 font-semibold text-gray-300">{{ $loop->iteration }}</td>

                            {{-- Name with Avatar --}}
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    {{-- Avatar --}}
                                    <div class="w-9 h-9 rounded-full overflow-hidden flex-shrink-0 bg-theme-from/10 flex items-center justify-center">
                                        @if($doctor->user?->image)
                                            <img src="{{ Storage::url($doctor->user->image) }}" alt="{{ $doctor->user->name }}" class="w-full h-full object-cover">
                                        @else
                                            <span class="text-sm font-bold text-theme-from">{{ strtoupper(substr($doctor->user->name ?? '?', 0, 1)) }}</span>
                                        @endif
                                    </div>
                                    <div>
                                        @if($isArchived)
                                            <p class="font-semibold text-gray-700">{{ $doctor->user->name ?? '—' }}</p>
                                        @else
                                            <a href="{{ route('doctors.show', $doctor->id) }}"
                                               class="font-semibold text-gray-800 hover:text-theme-from transition-colors duration-150">
                                                {{ $doctor->user->name ?? '—' }}
                                            </a>
                                        @endif
                                        @if($doctor->user?->full_name)
                                            <p class="text-xs text-gray-400">{{ $doctor->user->full_name }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            {{-- Specialty --}}
                            <td class="px-5 py-3.5">
                                <span class="text-sm text-gray-600">{{ $doctor->specialty ?? '—' }}</span>
                            </td>

                            {{-- Degree --}}
                            <td class="px-5 py-3.5">
                                <span class="text-sm text-gray-600">{{ $doctor->degree ?? '—' }}</span>
                            </td>

                            {{-- Status badge --}}
                             <td class="px-5 py-3.5">
                                 @if($doctor->is_active)
                                     <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-600 border border-emerald-100">
                                         Active
                                     </span>
                                 @else
                                     <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-500 border border-red-100">
                                         Inactive
                                     </span>
                                 @endif
                             </td>

                            {{-- Archive Date --}}
                            @if($isArchived)
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-1.5 text-gray-500">
                                        <x-icon name="calendar" class="w-3.5 h-3.5 text-amber-500" />
                                        {{ $doctor->deleted_at->format('M d, Y') }}
                                    </div>
                                </td>
                            @endif

                            {{-- Actions --}}
                            <x-table-actions
                                :archived="$isArchived"
                                :editRoute="route('doctors.edit', $doctor->id)"
                                :deleteRoute="route('doctors.destroy', $doctor->id)"
                                :restoreRoute="route('doctors.restore', $doctor->id)"
                                :itemName="$doctor->user->name ?? 'this doctor'"
                            />
                        </tr>
                    @empty
                        <x-message-empty colspan="{{ $isArchived ? 7 : 6 }}">
                            {{ $isArchived ? 'No archived doctors found' : 'No doctors found' }}
                        </x-message-empty>
                    @endforelse
                </tbody>
            </table>

            {{-- Pagination --}}
            @if($doctors->hasPages())
                <div class="px-5 py-3 border-t border-gray-100 bg-gray-50/50">
                    {{ $doctors->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
