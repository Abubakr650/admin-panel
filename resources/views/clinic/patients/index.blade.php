<x-app-layout>
@php $isArchived = request()->input('archived') === 'true'; @endphp
    <x-slot name="header">
        <div class="flex items-center justify-between w-full gap-4">
            {{-- Left: Title + Search + Gender Filter --}}
            <div class="flex items-center gap-3 flex-1 min-w-0">
                {{-- Page Title --}}
                <div class="shrink-0">
                    <p class="text-xs text-gray-400 uppercase tracking-widest font-semibold">Clinic</p>
                    <h1 class="text-lg font-bold text-gray-800 leading-tight">
                        {{ request()->input('archived') == 'true' ? 'Archived Patients' : 'Patients' }}
                    </h1>
                </div>

                {{-- Divider --}}
                <div class="h-8 w-px bg-gray-200 shrink-0"></div>

                {{-- Search Bar --}}
                <div class="w-64">
                    <x-search-bar
                        action="{{ route('patients.index') }}"
                        placeholder="Search by name or phone…"
                        :extraParams="array_filter(['gender' => request()->input('gender')])"
                    />
                </div>

                {{-- Gender Filter --}}
                @unless($isArchived)
                <div class="flex items-center gap-1 bg-gray-100 rounded-xl p-1 shrink-0">
                    @foreach(['all' => 'All', 'male' => 'Male', 'female' => 'Female'] as $value => $label)
                        @php
                            $activeGender = request()->input('gender', 'all');
                            $isActive = $activeGender === $value;
                            $href = route('patients.index', array_filter([
                                'search'   => request()->input('search'),
                                'gender'   => $value === 'all' ? null : $value,
                                'archived' => request()->input('archived'),
                            ]));
                        @endphp
                        <a href="{{ $href }}"
                           class="px-3 py-1 rounded-lg text-sm font-semibold transition-all duration-150
                                  {{ $isActive
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
                {{-- New Patient: يظهر فقط في الوضع العادي --}}
                @unless($isArchived)
                    <x-button-link href="{{ route('patients.create') }}">
                        <x-icon name="plus" class="w-4 h-4" />
                        New Patient
                    </x-button-link>
                @endunless

                <x-button-archive-view
                    :archived="$isArchived"
                    :indexRoute="route('patients.index')"
                    :archiveRoute="route('patients.index', ['archived' => 'true'])"
                    :archivedCount="$archivedCount ?? 0"
                />
            </div>
        </div>
    </x-slot>

    {{-- Page Body --}}
    <div class="space-y-4">

        {{-- Toast --}}
        @if(!$isArchived)
        {{-- ── Stats Row ─────────────────────────────────────────────── --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
            {{-- Total --}}
            <div class="relative overflow-hidden rounded-2xl bg-white border border-gray-100 shadow-sm p-4 flex items-center gap-4">
                <div class="flex-shrink-0 flex items-center justify-center w-11 h-11 rounded-xl bg-theme-from/10">
                    <x-icon name="users" class="w-5 h-5 text-theme-from" />
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total</p>
                    <p class="text-2xl font-extrabold text-gray-800">{{ $patients->total() }}</p>
                </div>
                <div class="absolute -right-3 -bottom-3 w-16 h-16 rounded-full bg-theme-from/10 opacity-60"></div>
            </div>
            {{-- Male --}}
            <div class="relative overflow-hidden rounded-2xl bg-white border border-gray-100 shadow-sm p-4 flex items-center gap-4">
                <div class="flex-shrink-0 flex items-center justify-center w-11 h-11 rounded-xl bg-blue-50">
                    <x-icon name="user" class="w-5 h-5 text-blue-500" />
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Male</p>
                    <p class="text-2xl font-extrabold text-gray-800">{{ $maleCount ?? '—' }}</p>
                </div>
                <div class="absolute -right-3 -bottom-3 w-16 h-16 rounded-full bg-blue-50 opacity-60"></div>
            </div>
            {{-- Female --}}
            <div class="relative overflow-hidden rounded-2xl bg-white border border-gray-100 shadow-sm p-4 flex items-center gap-4">
                <div class="flex-shrink-0 flex items-center justify-center w-11 h-11 rounded-xl bg-pink-50">
                    <x-icon name="user" class="w-5 h-5 text-pink-500" />
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Female</p>
                    <p class="text-2xl font-extrabold text-gray-800">{{ $femaleCount ?? '—' }}</p>
                </div>
                <div class="absolute -right-3 -bottom-3 w-16 h-16 rounded-full bg-pink-50 opacity-60"></div>
            </div>
        </div>
        @endif

        {{-- ── Table Card ────────────────────────────────────────────── --}}
        <div class="rounded-2xl bg-white border border-gray-100 shadow-sm overflow-hidden">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-gray-50/70">
                        
                    <th class="rounded-l-lg px-6 py-3  text-left text-md leading-4 font-semibold text-gray-500 uppercase tracking-wider">#</th>
                    <th class="rounded-l-lg px-6 py-3  text-left text-md leading-4 font-semibold text-gray-500 uppercase tracking-wider">Full Name</th>
                    <th class="rounded-l-lg px-6 py-3  text-left text-md leading-4 font-semibold text-gray-500 uppercase tracking-wider">Phone</th>
                    <th class="rounded-l-lg px-6 py-3  text-left text-md leading-4 font-semibold text-gray-500 uppercase tracking-wider">Gender</th>
                    <th class="rounded-l-lg px-6 py-3  text-left text-md leading-4 font-semibold text-gray-500 uppercase tracking-wider">Age (Years)</th>
                    @if($isArchived)
                        <th class="rounded-r-lg px-1 py-3 text-left text-md leading-4 font-semibold text-gray-500 uppercase tracking-wider">Archive Date</th>
                    @endif
                    <th class="rounded-l-lg px-6 py-3 bg-gray-50 text-right text-md leading-4 font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($patients as $patient)
                        <tr class="group hover:bg-theme-from/10 transition-colors duration-150">
                            {{-- Row number --}}
                            <td class="px-5 py-3.5  font-semibold text-gray-300">{{ $loop->iteration }}</td>

                            {{-- Name --}}
                            <td class="px-5 py-3.5">
                                @if($isArchived)
                                    <p class="font-semibold text-gray-700">{{ $patient->full_name }}</p>
                                @else
                                    <a href="{{ route('patients.show', $patient->id) }}"
                                        class="font-semibold text-gray-800 hover:text-theme-from transition-colors duration-150">
                                        {{ $patient->full_name }}
                                    </a>
                                @endif
                            </td>

                            {{-- Phone --}}
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-1.5 text-gray-600">
                                    @if($patient->phone)
                                        <x-icon name="phone" class="w-4 h-4 text-gray-300 flex-shrink-0" />
                                        {{ $patient->phone }}
                                    @else
                                        <span class="text-gray-400">No phone number</span>
                                    @endif
                                </div>
                            </td>

                            {{-- Gender badge --}}
                            <td class="px-5 py-3.5">
                                @if($patient->gender === 'male')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-600 border border-blue-100">
                                        <x-icon name="gender-male" class="w-3.5 h-3.5" :filled="true" />
                                        Male
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-pink-50 text-pink-600 border border-pink-100">
                                        <x-icon name="gender-female" class="w-3.5 h-3.5" :filled="true" />
                                        Female
                                    </span>
                                @endif
                            </td>

                            {{-- Age --}}
                            <td class="px-5 py-3.5">
                                <span class="font-semibold text-gray-700">{{ $patient->age }}</span>
                            </td>

                            {{-- Archive Date --}}
                            @if($isArchived)
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-1.5 text-gray-500">
                                        <x-icon name="calendar" class="w-3.5 h-3.5 text-amber-500" />
                                        {{ $patient->deleted_at->format('M d, Y') }}
                                    </div>
                                </td>
                            @endif

                            {{-- Actions --}}
                            <x-table-actions
                                :archived="$isArchived"
                                :editRoute="route('patients.edit', $patient->id)"
                                :deleteRoute="route('patients.destroy', $patient->id)"
                                :restoreRoute="route('patients.restore', $patient->id)"
                                :itemName="$patient->full_name"
                            />
                        </tr>
                    @empty
                        <x-message-empty colspan="{{ $isArchived ? 7 : 6 }}">
                            {{ $isArchived ? 'No archived patients found' : 'No patients found' }}
                        </x-message-empty>
                    @endforelse
                </tbody>
            </table>

            {{-- Pagination --}}
            @if($patients->hasPages())
                <div class="px-5 py-3 border-t border-gray-100 bg-gray-50/50">
                    {{ $patients->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
