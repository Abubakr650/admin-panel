<x-app-layout>
@php $isArchived = request()->input('archived') === 'true'; @endphp
    <x-slot name="header">
        <div class="flex items-center justify-between w-full gap-4">
            {{-- Left: Title + Search + Role Filter --}}
            <div class="flex items-center gap-3 flex-1 min-w-0">
                <div class="shrink-0">
                    <p class="text-xs text-gray-400 uppercase tracking-widest font-semibold">Admin</p>
                    <h1 class="text-lg font-bold text-gray-800 leading-tight">
                        {{ $isArchived ? 'Archived Users' : 'Users' }}
                    </h1>
                </div>
                <div class="h-8 w-px bg-gray-200 shrink-0"></div>
                <div class="w-64">
                    <x-search-bar
                        action="{{ route('users.index') }}"
                        placeholder="Search by name, email…"
                        :extraParams="array_filter(['role' => request()->input('role')])"
                    />
                </div>
                @unless($isArchived)
                <div class="flex items-center gap-1 bg-gray-100 rounded-xl p-1 shrink-0">
                    @php
                        $roles = ['all' => 'All'] + array_combine($rolesAvailable, array_map(fn($r) => ucwords(str_replace('-', ' ', $r)), $rolesAvailable));
                        $activeRole = request()->input('role', 'all');
                    @endphp
                    @foreach(['all' => 'All', 'doctor' => 'Doctors', 'staff' => 'Staff'] as $value => $label)
                        @php
                            $isActive = $activeRole === $value || ($value === 'staff' && in_array($activeRole, ['admin', 'accountant', 'receptionist', 'pharmacist', 'radiology-staff', 'warehouse-keeper']));
                        @endphp
                        <a href="{{ route('users.index', array_filter(['search' => request('search'), 'role' => $value === 'all' ? null : $value])) }}"
                           class="px-3 py-1 rounded-lg text-sm font-semibold transition-all duration-150
                                  {{ $isActive ? 'bg-white text-gray-800 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>
                @endunless
            </div>
            <div class="flex items-center gap-3 shrink-0">
                @unless($isArchived)
                    <x-button-link href="{{ route('users.create') }}">
                        <x-icon name="plus" class="w-4 h-4" />
                        New User
                    </x-button-link>
                @endunless
                <x-button-archive-view
                    :archived="$isArchived"
                    :indexRoute="route('users.index')"
                    :archiveRoute="route('users.index', ['archived' => 'true'])"
                    :archivedCount="$archivedCount ?? 0"
                />
            </div>
        </div>
    </x-slot>

    <div class="space-y-4">
        @if(!$isArchived)
        {{-- Stats Row --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
            @php
                $statCards = [
                    ['label' => 'Total Users',  'count' => $totalActive,    'icon' => 'users',    'bg' => 'primary'],
                    ['label' => 'Doctors',      'count' => $doctorCount,    'icon' => 'stethoscope','bg' => 'secondary'],
                    ['label' => 'Staff',        'count' => $staffCount,     'icon' => 'users',    'bg' => 'accent'],
                ];
                $bgMap = [
                    'primary'   => ['card' => 'bg-theme-from/10', 'text' => 'text-theme-from', 'dec' => 'bg-theme-from/10'],
                    'secondary' => ['card' => 'bg-teal-50',     'text' => 'text-teal-600',   'dec' => 'bg-teal-100'],
                    'accent'    => ['card' => 'bg-theme-to/10',   'text' => 'text-theme-to',   'dec' => 'bg-theme-from/10'],
                ];
            @endphp
            @foreach($statCards as $card)
                @php $c = $bgMap[$card['bg']]; @endphp
                <div class="relative overflow-hidden rounded-2xl bg-white border border-gray-100 shadow-sm p-4 flex items-center gap-4">
                    <div class="flex-shrink-0 flex items-center justify-center w-11 h-11 rounded-xl {{ $c['card'] }}">
                        <x-icon name="{{ $card['icon'] }}" class="w-5 h-5 {{ $c['text'] }}" />
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ $card['label'] }}</p>
                        <p class="text-2xl font-extrabold text-gray-800">{{ $card['count'] }}</p>
                    </div>
                    <div class="absolute -right-3 -bottom-3 w-16 h-16 rounded-full {{ $c['dec'] }} opacity-60"></div>
                </div>
            @endforeach
        </div>
        @endif

        {{-- Table Card --}}
        <div class="rounded-2xl bg-white border border-gray-100 shadow-sm overflow-hidden">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-gray-50/70">
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">#</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">User</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Role</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Gender</th>
                        @if($isArchived)
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Archive Date</th>
                        @endif
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($users as $user)
                        <tr class="group hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-5 py-3.5 font-semibold text-gray-300">{{ $loop->iteration }}</td>

                            {{-- User with Avatar --}}
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    {{-- Avatar --}}
                                    <div class="w-9 h-9 rounded-full overflow-hidden flex-shrink-0 bg-theme-from/10 flex items-center justify-center">
                                        @if($user->image)
                                            <img src="{{ $user->image_url }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                                        @else
                                            <span class="text-sm font-bold text-theme-from">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                        @endif
                                    </div>
                                    <div>
                                        @if($isArchived)
                                            <p class="font-semibold text-gray-700">{{ $user->name }}</p>
                                        @else
                                            <a href="{{ route('users.show', $user->id) }}"
                                               class="font-semibold text-gray-800 hover:text-theme-from transition-colors duration-150">
                                                {{ $user->name }}
                                            </a>
                                        @endif
                                        @if($user->full_name)
                                            <p class="text-xs text-gray-400">{{ $user->full_name }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <td class="px-5 py-3.5 text-sm text-gray-600">{{ $user->email }}</td>

                            {{-- Role Badge --}}
                            <td class="px-5 py-3.5">
                                @php
                                    $roleColors = [
                                        'admin'           => 'bg-violet-50 text-violet-700 border-violet-100',
                                        'doctor'          => 'bg-teal-50 text-teal-700 border-teal-100',
                                        'accountant'      => 'bg-blue-50 text-blue-700 border-blue-100',
                                        'receptionist'    => 'bg-amber-50 text-amber-700 border-amber-100',
                                        'pharmacist'      => 'bg-green-50 text-green-700 border-green-100',
                                        'radiology-staff' => 'bg-cyan-50 text-cyan-700 border-cyan-100',
                                        'warehouse-keeper'=> 'bg-orange-50 text-orange-700 border-orange-100',
                                    ];
                                    $roleClass = $roleColors[$user->role] ?? 'bg-gray-50 text-gray-600 border-gray-100';
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border {{ $roleClass }}">
                                    {{ ucwords(str_replace('-', ' ', $user->role)) }}
                                </span>
                            </td>

                            <td class="px-5 py-3.5 text-sm text-gray-600 capitalize">{{ $user->gender ?? '—' }}</td>

                            @if($isArchived)
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-1.5 text-gray-500">
                                        <x-icon name="calendar" class="w-3.5 h-3.5 text-amber-500" />
                                        {{ $user->deleted_at->format('M d, Y') }}
                                    </div>
                                </td>
                            @endif
                            
                            {{-- If Admin don't allow edit or archive --}}
                            @if($user->role !== 'admin')
                                <x-table-actions
                                    :archived="$isArchived"
                                    :editRoute="route('users.edit', $user->id)"
                                    :deleteRoute="route('users.destroy', $user->id)"
                                    :restoreRoute="route('users.restore', $user->id)"
                                    :itemName="$user->name"
                                />
                            @endif
                        </tr>
                    @empty
                        <x-message-empty colspan="{{ $isArchived ? 7 : 6 }}">
                            {{ $isArchived ? 'No archived users found' : 'No users found' }}
                        </x-message-empty>
                    @endforelse
                </tbody>
            </table>

            @if($users->hasPages())
                <div class="px-5 py-3 border-t border-gray-100 bg-gray-50/50">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>