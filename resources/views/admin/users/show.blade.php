<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between w-full">
            <div class="flex items-center gap-3">
                <x-button-back href="{{ route('users.index') }}" label="Users" />
                <div class="h-8 w-px bg-gray-200"></div>
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wider font-medium">Admin › Users</p>
                    <h1 class="text-xl font-bold text-gray-800">{{ $user->name }}</h1>
                </div>
            </div>
            {{-- If Admin don't allow edit or archive --}}
            @if($user->role !== 'admin')
            <div class="flex items-center gap-2">
                <x-button-link href="{{ route('users.edit', $user) }}">Edit</x-button-link>
                <form action="{{ route('users.destroy', $user) }}" method="POST"
                      onsubmit="return confirm('Archive this user?')">
                    @csrf @method('DELETE')
                    <x-button-archive>Archive</x-button-archive>
                </form>
            </div>
            @endif
        </div>
    </x-slot>

    <div class="space-y-5">
        {{-- Profile Header Card --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="bg-gradient-to-r from-theme-from to-theme-to px-8 py-6">
                <div class="flex items-center gap-5">
                    {{-- Avatar --}}
                    <div class="w-16 h-16 rounded-2xl overflow-hidden flex-shrink-0 border-4 border-white/30 shadow-lg bg-white/20 flex items-center justify-center">
                        @if($user->image_url)
                            <img src="{{ $user->image_url }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                        @else
                            <span class="text-3xl font-extrabold text-white">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <h2 class="text-white text-2xl font-extrabold truncate">{{ $user->name }}</h2>
                        @if($user->full_name)
                            <p class="text-white text-sm mt-0.5">{{ $user->full_name }}</p>
                        @endif
                        <div class="flex items-center gap-3 mt-3 flex-wrap">
                            @php
                                $roleColors = [
                                    'admin'           => 'bg-violet-200/40 text-white border-violet-200/30',
                                    'doctor'          => 'bg-teal-200/40 text-white border-teal-200/30',
                                    'accountant'      => 'bg-blue-200/40 text-white border-blue-200/30',
                                    'receptionist'    => 'bg-amber-200/40 text-white border-amber-200/30',
                                    'pharmacist'      => 'bg-green-200/40 text-white border-green-200/30',
                                    'radiology-staff' => 'bg-cyan-200/40 text-white border-cyan-200/30',
                                    'warehouse-keeper'=> 'bg-orange-200/40 text-white border-orange-200/30',
                                ];
                                $badgeClass = $roleColors[$user->role] ?? 'bg-white/20 text-white border-white/20';
                            @endphp
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border {{ $badgeClass }}">
                                {{ ucwords(str_replace('-', ' ', $user->role)) }}
                            </span>
                             @if($user->gender === 'male')
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-white/20 text-white backdrop-blur-sm border border-white/10">
                                    <x-icon name="gender-male" class="w-3.5 h-3.5" :filled="true" />
                                    Male
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-white/20 text-white backdrop-blur-sm border border-white/10">
                                    <x-icon name="gender-female" class="w-3.5 h-3.5" :filled="true" />
                                    Female
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Info Grid --}}
            <div class="p-6 grid grid-cols-2 sm:grid-cols-4 gap-4">
                {{-- Email --}}
                <div class="flex items-center gap-3 p-3.5 rounded-xl bg-gray-50/80 border border-gray-100">
                    <div class="flex-shrink-0 flex items-center justify-center w-10 h-10 rounded-xl bg-theme-from/10">
                        <x-icon name="mail" class="w-5 h-5 text-theme-from" />
                    </div>
                    <div class="min-w-0">
                        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Email</p>
                        <p class="text-sm font-semibold text-gray-800 truncate">{{ $user->email }}</p>
                    </div>
                </div>

                {{-- Phone --}}
                <div class="flex items-center gap-3 p-3.5 rounded-xl bg-gray-50/80 border border-gray-100">
                    <div class="flex-shrink-0 flex items-center justify-center w-10 h-10 rounded-xl bg-theme-from/10">
                        <x-icon name="phone" class="w-5 h-5 text-theme-from" />
                    </div>
                    <div class="min-w-0">
                        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Phone</p>
                        <p class="text-sm font-semibold text-gray-800 truncate">{{ $user->phone ?? '—' }}</p>
                    </div>
                </div>

                {{-- Birth Date --}}
                <div class="flex items-center gap-3 p-3.5 rounded-xl bg-gray-50/80 border border-gray-100">
                    <div class="flex-shrink-0 flex items-center justify-center w-10 h-10 rounded-xl bg-theme-from/10">
                        <x-icon name="calendar" class="w-5 h-5 text-theme-from" />
                    </div>
                    <div class="min-w-0">
                        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Birth Date</p>
                        <p class="text-sm font-semibold text-gray-800 truncate">{{ $user->birth_date?->format('M d, Y') ?? '—' }}</p>
                    </div>
                </div>

                {{-- Registered --}}
                <div class="flex items-center gap-3 p-3.5 rounded-xl bg-gray-50/80 border border-gray-100">
                    <div class="flex-shrink-0 flex items-center justify-center w-10 h-10 rounded-xl bg-theme-from/10">
                        <x-icon name="badge-check" class="w-5 h-5 text-theme-from" />
                    </div>
                    <div class="min-w-0">
                        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Registered</p>
                        <p class="text-sm font-semibold text-gray-800 truncate">{{ $user->created_at->format('M d, Y') }}</p>
                    </div>
                </div>
            </div>

            @if($user->address)
            <div class="px-6 pb-6">
                <div class="flex items-start gap-3 p-3.5 rounded-xl bg-gray-50/80 border border-gray-100">
                    <div class="flex-shrink-0 flex items-center justify-center w-10 h-10 rounded-xl bg-theme-from/10 mt-0.5">
                        <x-icon name="location" class="w-5 h-5 text-theme-from" />
                    </div>
                    <div>
                        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Address</p>
                        <p class="text-sm font-semibold text-gray-800 mt-0.5">{{ $user->address }}</p>
                    </div>
                </div>
            </div>
            @endif
        </div>

        {{-- Doctor Link Card --}}
        @if($user->doctor)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-theme-from/10">
                        <x-icon name="stethoscope" class="w-5 h-5 text-theme-from" />
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Doctor Profile</p>
                        <p class="text-sm font-semibold text-gray-800">{{ $user->doctor->specialty ?? '—' }}
                            @if($user->doctor->degree)
                                <span class="text-gray-400 font-normal">— {{ $user->doctor->degree }}</span>
                            @endif
                        </p>
                    </div>
                </div>
                <a href="{{ route('doctors.show', $user->doctor->id) }}"
                   class="inline-flex items-center gap-1 text-theme-from hover:opacity-80 font-semibold text-xs px-2.5 py-1 rounded-lg bg-theme-from/10 border border-theme-from/20 transition-colors duration-150">
                    View Doctor Profile
                </a>
            </div>
        </div>
        @endif
    </div>
</x-app-layout>
