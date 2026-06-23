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
                        {{ $isArchived ? 'Archived Appointments' : 'Appointments' }}
                    </h1>
                </div>

                {{-- Divider --}}
                <div class="h-8 w-px bg-gray-200 shrink-0"></div>

                {{-- Search Bar --}}
                <div class="w-64">
                    <x-search-bar
                        action="{{ route('appointments.index') }}"
                        placeholder="Search by patient or doctor…"
                        :extraParams="array_filter([
                            'status' => request()->input('status'),
                            'doctor_id' => request()->input('doctor_id'),
                            'from' => request()->input('from'),
                            'to' => request()->input('to'),
                            'archived' => request()->input('archived')
                        ])"
                    />
                </div>

                {{-- Status Filter --}}
                @unless($isArchived)
                    <form action="{{ route('appointments.index') }}" method="GET" class="flex items-center gap-3">
                        @if(request('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif
                        @if(request('archived')) <input type="hidden" name="archived" value="{{ request('archived') }}"> @endif

                        {{-- Status Dropdown --}}
                        <div class="relative min-w-[140px]" x-data="{ open: false, status: '{{ request('status', 'scheduled') }}' }">
                            <select name="status" onchange="this.form.submit()"
                                    class="w-full pl-3 pr-10 py-2 text-sm font-semibold text-gray-700 bg-gray-50 border border-gray-200 rounded-xl appearance-none focus:outline-none focus:ring-2 focus:ring-theme-from/30 focus:border-theme-from hover:bg-white transition-all cursor-pointer"
                                    style="background-image: url('data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' fill=\'none\' viewBox=\'0 0 24 24\' stroke=\'%236b7280\'%3E%3Cpath stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M19 9l-7 7-7-7\'/%3E%3C/svg%3E'); background-position: right 12px center; background-size: 16px; background-repeat: no-repeat;">
                                <option value="all" {{ request('status') === 'all' ? 'selected' : '' }}>All Status</option>
                                <option value="scheduled" {{ request('status', 'scheduled') === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                <option value="no_show"   {{ request('status') === 'no_show'   ? 'selected' : '' }}>No Show</option>
                            </select>
                        </div>

                        {{-- Doctor Dropdown --}}
                        <div class="relative min-w-[160px]">
                            <select name="doctor_id" onchange="this.form.submit()"
                                    class="w-full pl-3 pr-10 py-2 text-sm font-semibold text-gray-700 bg-gray-50 border border-gray-200 rounded-xl appearance-none focus:outline-none focus:ring-2 focus:ring-theme-from/30 focus:border-theme-from hover:bg-white transition-all cursor-pointer"
                                    style="background-image: url('data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' fill=\'none\' viewBox=\'0 0 24 24\' stroke=\'%236b7280\'%3E%3Cpath stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M19 9l-7 7-7-7\'/%3E%3C/svg%3E'); background-position: right 12px center; background-size: 16px; background-repeat: no-repeat;">
                                <option value="">All Doctors</option>
                                @foreach($allDoctors as $doc)
                                    <option value="{{ $doc->id }}" {{ request('doctor_id') == $doc->id ? 'selected' : '' }}>
                                        Dr. {{ $doc->user->name ?? 'Unknown' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Date Filters --}}
                        <div class="flex items-center gap-1.5 bg-gray-50 border border-gray-200 shadow-sm rounded-xl px-2.5 py-1.5 hover:bg-white transition-colors duration-200 focus-within:ring-2 focus-within:ring-theme-from/30 focus-within:border-theme-from">
                            <x-icon name="calendar" class="w-4 h-4 text-theme-from/60" />
                            <input type="date" name="from" value="{{ request('from', now()->format('Y-m-d')) }}"
                                   class="bg-transparent border-none p-0 text-sm font-medium text-gray-700 focus:ring-0 w-[110px]"
                                   onchange="this.form.submit()">
                            <span class="text-gray-300 font-bold mx-0.5">→</span>
                            <input type="date" name="to" value="{{ request('to', now()->format('Y-m-d')) }}"
                                   class="bg-transparent border-none p-0 text-sm font-medium text-gray-700 focus:ring-0 w-[110px]"
                                   onchange="this.form.submit()">
                        </div>

                        @if(request('from') || request('to') || request('doctor_id') || request('status') !== 'scheduled')
                            <a href="{{ route('appointments.index') }}" 
                               class="inline-flex items-center justify-center w-9 h-9 bg-gray-100 hover:bg-red-50 text-gray-400 hover:text-red-500 rounded-xl transition-all duration-200"
                               title="Clear All Filters">
                                <x-icon name="x" class="w-4 h-4" />
                            </a>
                        @endif
                    </form>
                @endunless
            </div>

            {{-- Right: Action Buttons --}}
            <div class="flex items-center gap-3 shrink-0">
                @unless($isArchived)
                    <x-button-link href="{{ route('appointments.create') }}">
                        <x-icon name="plus" class="w-4 h-4" />
                        New Appointment
                    </x-button-link>
                @endunless

                <x-button-archive-view
                    :archived="$isArchived"
                    :indexRoute="route('appointments.index')"
                    :archiveRoute="route('appointments.index', ['archived' => 'true'])"
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
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            {{-- Total --}}
            <div class="relative overflow-hidden rounded-2xl bg-white border border-gray-100 shadow-sm p-4 flex items-center gap-4">
                <div class="flex-shrink-0 flex items-center justify-center w-11 h-11 rounded-xl bg-theme-from/10">
                    <x-icon name="calendar" class="w-5 h-5 text-theme-from" />
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total</p>
                    <p class="text-2xl font-extrabold text-gray-800">{{ $appointments->total() }}</p>
                </div>
                <div class="absolute -right-3 -bottom-3 w-16 h-16 rounded-full bg-theme-from/10 opacity-60"></div>
            </div>
            {{-- Scheduled --}}
            <div class="relative overflow-hidden rounded-2xl bg-white border border-gray-100 shadow-sm p-4 flex items-center gap-4">
                <div class="flex-shrink-0 flex items-center justify-center w-11 h-11 rounded-xl bg-blue-50">
                    <x-icon name="clock" class="w-5 h-5 text-blue-500" />
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Scheduled</p>
                    <p class="text-2xl font-extrabold text-gray-800">{{ $scheduledCount ?? 0 }}</p>
                </div>
                <div class="absolute -right-3 -bottom-3 w-16 h-16 rounded-full bg-blue-50 opacity-60"></div>
            </div>
            {{-- Completed --}}
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
            {{-- Cancelled --}}
            <div class="relative overflow-hidden rounded-2xl bg-white border border-gray-100 shadow-sm p-4 flex items-center gap-4">
                <div class="flex-shrink-0 flex items-center justify-center w-11 h-11 rounded-xl bg-red-50">
                    <x-icon name="x-circle" class="w-5 h-5 text-red-500" />
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Cancelled</p>
                    <p class="text-2xl font-extrabold text-gray-800">{{ $cancelledCount ?? 0 }}</p>
                </div>
                <div class="absolute -right-3 -bottom-3 w-16 h-16 rounded-full bg-red-50 opacity-60"></div>
            </div>
        </div>
        @endif

        {{-- ── Table Card ────────────────────────────────────────────── --}}
        <div class="rounded-2xl bg-white border border-gray-100 shadow-sm overflow-hidden">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-gray-50/70">
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">#</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Patient</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Doctor</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Date & Time</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        @if($isArchived)
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Archive Date</th>
                        @endif
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($appointments as $appointment)
                        @php
                            $statusColors = [
                                'scheduled'  => 'bg-blue-50 text-blue-600 border-blue-100',
                                'completed'  => 'bg-green-50 text-green-600 border-green-100',
                                'cancelled'  => 'bg-red-50 text-red-600 border-red-100',
                                'no_show'    => 'bg-amber-50 text-amber-600 border-amber-100',
                            ];
                            $statusLabel = ucfirst(str_replace('_', ' ', $appointment->appointment_status));
                            $color = $statusColors[$appointment->appointment_status] ?? 'bg-gray-50 text-gray-600 border-gray-100';
                        @endphp
                        <tr class="group hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-6 py-4 font-semibold text-gray-300">{{ $loop->iteration }}</td>
                            <td class="px-6 py-4">
                                <a href="{{ route('appointments.show', $appointment->id) }}" class="font-semibold text-gray-800 hover:text-theme-from transition-colors">
                                    {{ $appointment->patient->full_name ?? '—' }}
                                </a>
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                <div class="flex items-center gap-2">
                                    <span class="font-medium">{{ $appointment->doctor->user->name ?? '—' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-0.5">
                                    <div class="flex items-center gap-1.5 text-gray-700 font-semibold text-sm">
                                        <x-icon name="calendar" class="w-3.5 h-3.5 text-gray-400" />
                                        {{ $appointment->appointment_date?->format('M d, Y') }}
                                    </div>
                                    <div class="flex items-center gap-1.5 text-gray-400 text-xs">
                                        <x-icon name="clock" class="w-3.5 h-3.5" />
                                        {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border {{ $color }}">
                                    {{ $statusLabel }}
                                </span>
                            </td>
                            @if($isArchived)
                                <td class="px-6 py-4 text-gray-500 text-sm">
                                    {{ $appointment->deleted_at->format('M d, Y') }}
                                </td>
                            @endif
                            <x-table-actions
                                :archived="$isArchived"
                                :editRoute="route('appointments.edit', $appointment->id)"
                                :deleteRoute="route('appointments.destroy', $appointment->id)"
                                :restoreRoute="route('appointments.restore', $appointment->id)"
                                :itemName="'Appointment for ' . ($appointment->patient->full_name ?? 'N/A')"
                            >
                                @if(!$isArchived && $appointment->appointment_status === 'scheduled')
                                    <div x-data="{ showCompleteModal: false }" class="inline-block">
                                        <button type="button" @click="showCompleteModal = true"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-green-50 text-green-600 font-semibold border border-green-50 hover:bg-green-100 hover:border-green-200 hover:shadow hover:-translate-y-0.5 active:scale-95 transition-all duration-200"
                                                title="Mark as Completed">
                                            <x-icon name="check-circle" class="w-3.5 h-3.5" />
                                            Complete
                                        </button>

                                        <div x-show="showCompleteModal" style="display: none;" class="fixed inset-0 z-[100] overflow-y-auto">
                                            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                                <div x-show="showCompleteModal" @click="showCompleteModal = false" x-transition.opacity class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity"></div>
                                                
                                                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                                                
                                                <div x-show="showCompleteModal"
                                                     x-transition:enter="ease-out duration-300"
                                                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                                     x-transition:leave="ease-in duration-200"
                                                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                                                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                                     class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full border border-gray-100 p-6 relative z-[101]">
                                                    
                                                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                                                        <x-icon name="check-circle" class="w-5 h-5 text-green-500" />
                                                        Complete Appointment
                                                    </h3>
                                                    
                                                    <div class="mb-5 p-4 rounded-xl bg-blue-50/50 border border-blue-100/50 flex items-center justify-between">
                                                        <div class="flex items-center gap-2 text-blue-800 font-semibold text-sm">
                                                            <x-icon name="currency-dollar" class="w-4 h-4 text-blue-500" />
                                                            Consultation Fee
                                                        </div>
                                                        <span class="text-lg font-black text-blue-600">${{ number_format($consultationPrice, 2) }}</span>
                                                    </div>
                                                    
                                                    <form action="{{ route('appointments.complete', $appointment->id) }}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        
                                                        <div class="space-y-4">
                                                            <div>
                                                                <label class="block text-sm font-semibold text-gray-700 mb-1">Amount Paid <span class="text-xs text-gray-400 font-normal">(Optional)</span></label>
                                                                <div class="relative">
                                                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                                        <span class="text-gray-500 sm:text-sm">$</span>
                                                                    </div>
                                                                    <input type="number" step="0.01" name="amount_paid" value="{{ $consultationPrice }}" class="w-full pl-7 rounded-xl border-gray-200 focus:ring-theme-from focus:border-theme-from bg-gray-50/50 font-semibold text-gray-800" placeholder="0.00">
                                                                </div>
                                                            </div>
                                                            
                                                            <div>
                                                                <label class="block text-sm font-semibold text-gray-700 mb-1">Payment Method</label>
                                                                <select name="payment_method" class="w-full rounded-xl border-gray-200 focus:ring-theme-from focus:border-theme-from bg-gray-50/50 font-medium text-gray-700">
                                                                    <option value="cash">Cash</option>
                                                                    <option value="card">Card</option>
                                                                    <option value="bank_transfer">Bank Transfer</option>
                                                                    <option value="other">Other</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="mt-6 flex items-center justify-end gap-3">
                                                            <button type="button" @click="showCompleteModal = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-semibold transition-colors">Cancel</button>
                                                            <button type="submit" class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-xl font-semibold transition-colors shadow-sm shadow-green-500/20">Confirm</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </x-table-actions>
                        </tr>
                    @empty
                        <x-message-empty colspan="{{ $isArchived ? 7 : 6 }}">
                            {{ $isArchived ? 'No archived appointments found' : 'No appointments found' }}
                        </x-message-empty>
                    @endforelse
                </tbody>
            </table>

            @if($appointments->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
                    {{ $appointments->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
