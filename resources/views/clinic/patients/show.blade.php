<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between w-full">
            {{-- Left: Back Button + Breadcrumb + Patient Name --}}
            <div class="flex items-center gap-3">
                <x-button-back href="{{ route('patients.index') }}" label="Patients" />
                <div class="h-8 w-px bg-gray-200"></div>
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wider font-medium">Clinic › Patients</p>
                    <h1 class="text-xl font-bold text-gray-800">{{ $patient->full_name }}</h1>
                </div>
            </div>
            {{-- Right: Action Buttons --}}
            <div class="flex items-center gap-2">
                <x-button-link href="{{ route('patients.edit', $patient) }}">
                    Edit
                </x-button-link>
                <form action="{{ route('patients.destroy', $patient) }}" method="POST"
                      onsubmit="return confirm('Are you sure you want to archive this patient?')">
                    @csrf @method('DELETE')
                    <x-button-archive>Archive</x-button-archive>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="space-y-5">
        {{-- ── Main Wrapper Card ─────────────────────────────────────── --}}
        <div class="max-w-full mx-auto bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">

            {{-- ── Patient Profile Header ────────────────────────────── --}}
            <div class="bg-gradient-to-r from-theme-from to-theme-to px-8 py-6">
                <div class="flex items-center gap-5">
                    {{-- Avatar --}}
                    <div class="w-16 h-16 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center shadow-inner">
                        <x-icon name="user" class="w-8 h-8 text-white" />
                    </div>
                    <div class="flex-1">
                        <h2 class="text-white text-2xl font-bold">{{ $patient->full_name }}</h2>
                        <div class="flex items-center gap-3 mt-1.5">
                            {{-- Gender Badge --}}
                            @if($patient->gender === 'male')
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
                            <span class="text-sky-100 text-sm">{{ $patient->age ?? '—' }} years old</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Patient Info Cards ────────────────────────────────── --}}
            <div class="px-8 py-6 border-b border-gray-100">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    {{-- Phone --}}
                    <div class="flex items-center gap-3 p-3.5 rounded-xl bg-gray-50/80 border border-gray-100">
                        <div class="flex-shrink-0 flex items-center justify-center w-10 h-10 rounded-xl bg-theme-from/10">
                            <x-icon name="phone" class="w-5 h-5 text-theme-from" />
                        </div>
                        <div class="min-w-0">
                            <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Phone</p>
                            <p class="text-sm font-medium text-gray-800 truncate">{{ $patient->phone ?? '—' }}</p>
                        </div>
                    </div>
                    {{-- Address --}}
                    <div class="flex items-center gap-3 p-3.5 rounded-xl bg-gray-50/80 border border-gray-100">
                        <div class="flex-shrink-0 flex items-center justify-center w-10 h-10 rounded-xl bg-theme-from/10">
                            <x-icon name="location" class="w-5 h-5 text-theme-from" />
                        </div>
                        <div class="min-w-0">
                            <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Address</p>
                            <p class="text-sm font-medium text-gray-800 truncate">{{ $patient->address ?? '—' }}</p>
                        </div>
                    </div>
                    {{-- Birth Date --}}
                    <div class="flex items-center gap-3 p-3.5 rounded-xl bg-gray-50/80 border border-gray-100">
                        <div class="flex-shrink-0 flex items-center justify-center w-10 h-10 rounded-xl bg-theme-to/10">
                            <x-icon name="calendar" class="w-5 h-5 text-theme-to" />
                        </div>
                        <div class="min-w-0">
                            <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Birth Date</p>
                            <p class="text-sm font-medium text-gray-800 truncate">{{ $patient->birth_date?->format('M d, Y') ?? '—' }}</p>
                        </div>
                    </div>
                    {{-- Registered --}}
                    <div class="flex items-center gap-3 p-3.5 rounded-xl bg-gray-50/80 border border-gray-100">
                        <div class="flex-shrink-0 flex items-center justify-center w-10 h-10 rounded-xl bg-theme-from/10">
                            <x-icon name="info" class="w-5 h-5 text-theme-from" />
                        </div>
                        <div class="min-w-0">
                            <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Registered</p>
                            <p class="text-sm font-medium text-gray-800 truncate">{{ $patient->created_at->format('M d, Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Tabs Navigation  ── --}}
            <div class="border-b border-gray-200 px-8">
                <nav class="-mb-px flex space-x-8">
                    <a href="{{ route('patients.show', ['patient' => $patient->id, 'tab' => 'appointments']) }}"
                       class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-150
                              {{ $activeTab == 'appointments' ? 'border-theme-from text-theme-from' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        <x-icon name="calendar" class="w-4 h-4 inline-block mr-1.5 -mt-0.5" />
                        Appointments
                        <span class="ml-1.5 px-2 py-0.5 rounded-full text-xs font-bold
                                     {{ $activeTab == 'appointments' ? 'bg-theme-from/10 text-theme-from' : 'bg-gray-100 text-gray-500' }}">
                            {{ $counts['appointments'] }}
                        </span>
                    </a>
                    <a href="{{ route('patients.show', ['patient' => $patient->id, 'tab' => 'invoices']) }}"
                       class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-150
                              {{ $activeTab == 'invoices' ? 'border-theme-from text-theme-from' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        <svg class="w-4 h-4 inline-block mr-1.5 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/>
                        </svg>
                        Invoices
                        <span class="ml-1.5 px-2 py-0.5 rounded-full text-xs font-bold
                                     {{ $activeTab == 'invoices' ? 'bg-theme-from/10 text-theme-from' : 'bg-gray-100 text-gray-500' }}">
                            {{ $counts['invoices'] }}
                        </span>
                    </a>
                    <a href="{{ route('patients.show', ['patient' => $patient->id, 'tab' => 'radiology']) }}"
                       class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-150
                              {{ $activeTab == 'radiology' ? 'border-theme-from text-theme-from' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        <svg class="w-4 h-4 inline-block mr-1.5 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Radiology
                        <span class="ml-1.5 px-2 py-0.5 rounded-full text-xs font-bold
                                     {{ $activeTab == 'radiology' ? 'bg-theme-from/10 text-theme-from' : 'bg-gray-100 text-gray-500' }}">
                            {{ $counts['radiology'] }}
                        </span>
                    </a>
                    <a href="{{ route('patients.show', ['patient' => $patient->id, 'tab' => 'orthodontics']) }}"
                       class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-150
                              {{ $activeTab == 'orthodontics' ? 'border-theme-from text-theme-from' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        <svg class="w-4 h-4 inline-block mr-1.5 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Orthodontics
                        <span class="ml-1.5 px-2 py-0.5 rounded-full text-xs font-bold
                                     {{ $activeTab == 'orthodontics' ? 'bg-theme-from/10 text-theme-from' : 'bg-gray-100 text-gray-500' }}">
                            {{ $counts['orthodontics'] }}
                        </span>
                    </a>
                </nav>
            </div>

            {{-- ── Tab Content ───────────────────────────────────────── --}}
            <div class="p-6">

                {{-- ═══ Appointments Tab ═══ --}}
                @if($activeTab == 'appointments')
                <div class="block">
                    <x-search-bar :action="route('patients.show', $patient->id)" placeholder="Search by doctor..." :extraParams="['tab' => 'appointments']">
                        <div class="flex items-center gap-2 flex-nowrap">
                            <select name="status" @change="$refs.form.submit()" 
                                    class="block w-full sm:w-40 py-2 pl-3 pr-10 border border-gray-200 rounded-xl text-sm focus:ring-theme-from focus:border-theme-from font-medium bg-white flex-shrink-0">
                                <option value="">All Statuses</option>
                                <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            </select>
                        </div>
                    </x-search-bar>

                    <div class="mt-4">
                        <table class="min-w-full rounded-lg overflow-hidden">
                        <thead>
                            <tr class="bg-gray-50/70">
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">#</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Time</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Doctor</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($appointments as $appt)
                                <tr class="group hover:bg-theme-from/10 transition-colors duration-150">
                                    <td class="px-6 py-3 font-semibold text-gray-300">{{ ($appointments->currentPage() - 1) * $appointments->perPage() + $loop->iteration }}</td>
                                    <td class="px-6 py-3">
                                        <span class="text-sm text-gray-700">{{ $appt->appointment_date?->format('M d, Y') ?? '—' }}</span>
                                    </td>
                                    <td class="px-6 py-3">
                                        <span class="text-sm text-gray-600">{{ $appt->appointment_time ?? '—' }}</span>
                                    </td>
                                    <td class="px-6 py-3">
                                        <span class="text-sm font-medium text-gray-700">{{ $appt->doctor->user->name ?? '—' }}</span>
                                    </td>
                                    <td class="px-6 py-3">
                                        @php
                                            $statusColors = [
                                                'scheduled'   => 'bg-blue-50 text-blue-600 border-blue-100',
                                                'completed'   => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                                                'cancelled'   => 'bg-red-50 text-red-600 border-red-100',
                                                'in_progress' => 'bg-amber-50 text-amber-600 border-amber-100',
                                            ];
                                            $color = $statusColors[$appt->appointment_status] ?? 'bg-gray-50 text-gray-600 border-gray-100';
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border {{ $color }}">
                                            {{ ucfirst(str_replace('_', ' ', $appt->appointment_status)) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-3 text-right">
                                        <a href="{{ route('appointments.show', $appt->id) }}" class="inline-flex items-center gap-1 text-theme-from hover:opacity-80 font-semibold text-xs px-2.5 py-1 rounded-lg bg-theme-from/10 border border-theme-from/20 transition-colors duration-150">View</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-10 text-center">
                                        <div class="flex flex-col items-center gap-2">
                                            <x-icon name="calendar" class="w-10 h-10 text-gray-200" />
                                            <p class="text-sm text-gray-400 font-medium">No appointments found</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    @if(isset($appointments) && $appointments->hasPages())
                        <div class="mt-6 px-2">
                            {{ $appointments->links() }}
                        </div>
                    @endif
                </div>
                @endif

                {{-- ═══ Invoices Tab ═══ --}}
                @if($activeTab == 'invoices')
                <div class="block">
                    <x-search-bar :action="route('patients.show', $patient->id)" placeholder="Search by invoice number..." :extraParams="['tab' => 'invoices']">
                        <div class="flex items-center gap-2 flex-nowrap">
                            <select name="status" @change="$refs.form.submit()" 
                                    class="block w-full sm:w-40 py-2 pl-3 pr-10 border border-gray-200 rounded-xl text-sm focus:ring-theme-from focus:border-theme-from font-medium bg-white flex-shrink-0">
                                <option value="">All Statuses</option>
                                <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                                <option value="unpaid" {{ request('status') == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                                <option value="partial" {{ request('status') == 'partial' ? 'selected' : '' }}>Partial</option>
                            </select>
                        </div>
                    </x-search-bar>

                    <div class="mt-4">
                        <table class="min-w-full rounded-lg overflow-hidden">
                        <thead>
                            <tr class="bg-gray-50/70">
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">#</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Invoice Number</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Total</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($invoices as $invoice)
                                <tr class="group hover:bg-theme-from/10 transition-colors duration-150">
                                    <td class="px-6 py-3 font-semibold text-gray-300">{{ ($invoices->currentPage() - 1) * $invoices->perPage() + $loop->iteration }}</td>
                                    <td class="px-6 py-3">
                                        <span class="text-sm font-medium text-gray-800">{{ $invoice->invoice_number ?? '—' }}</span>
                                    </td>
                                    <td class="px-6 py-3">
                                        <span class="text-sm text-gray-600">{{ $invoice->created_at?->format('M d, Y') ?? '—' }}</span>
                                    </td>
                                    <td class="px-6 py-3">
                                        <span class="text-sm font-bold text-gray-800">{{ number_format($invoice->total_amount ?? 0, 2) }}</span>
                                    </td>
                                    <td class="px-6 py-3">
                                        @php
                                            $invStatusColors = [
                                                'paid'      => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                                                'unpaid'    => 'bg-red-50 text-red-600 border-red-100',
                                                'partial'   => 'bg-amber-50 text-amber-600 border-amber-100',
                                            ];
                                            $invColor = $invStatusColors[$invoice->status ?? ''] ?? 'bg-gray-50 text-gray-600 border-gray-100';
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border {{ $invColor }}">
                                            {{ ucfirst($invoice->status ?? '—') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-3 text-right">
                                        <a href="{{ route('invoices.show', $invoice->id) }}" class="inline-flex items-center gap-1 text-theme-from hover:opacity-80 font-semibold text-xs px-2.5 py-1 rounded-lg bg-theme-from/10 border border-theme-from/20 transition-colors duration-150">View</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-10 text-center">
                                        <div class="flex flex-col items-center gap-2">
                                            <svg class="w-10 h-10 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/></svg>
                                            <p class="text-sm text-gray-400 font-medium">No invoices found</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    @if(isset($invoices) && $invoices->hasPages())
                        <div class="mt-6 px-2">
                            {{ $invoices->links() }}
                        </div>
                    @endif
                </div>
                @endif

                {{-- ═══ Radiology Tab ═══ --}}
                @if($activeTab == 'radiology')
                <div class="block">
                    <x-search-bar :action="route('patients.show', $patient->id)" placeholder="Search by scan type..." :extraParams="['tab' => 'radiology']" />

                    <div class="mt-4">
                        <table class="min-w-full rounded-lg overflow-hidden">
                        <thead>
                            <tr class="bg-gray-50/70">
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">#</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Notes</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($radiologies as $rad)
                                <tr class="group hover:bg-theme-from/10 transition-colors duration-150">
                                    <td class="px-6 py-3 font-semibold text-gray-300">{{ ($radiologies->currentPage() - 1) * $radiologies->perPage() + $loop->iteration }}</td>
                                    <td class="px-6 py-3">
                                        <span class="text-sm font-medium text-gray-800">{{ $rad->type ?? '—' }}</span>
                                    </td>
                                    <td class="px-6 py-3">
                                        <span class="text-sm text-gray-600">{{ $rad->created_at?->format('M d, Y') ?? '—' }}</span>
                                    </td>
                                    <td class="px-6 py-3">
                                        <span class="text-sm text-gray-600 line-clamp-1">{{ $rad->notes ?? '—' }}</span>
                                    </td>
                                    <td class="px-6 py-3 text-right">
                                        <a href="{{ route('radiology.scans.show', $rad->id) }}" class="inline-flex items-center gap-1 text-theme-from hover:opacity-80 font-semibold text-xs px-2.5 py-1 rounded-lg bg-theme-from/10 border border-theme-from/20 transition-colors duration-150">View</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-10 text-center">
                                        <div class="flex flex-col items-center gap-2">
                                            <svg class="w-10 h-10 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            <p class="text-sm text-gray-400 font-medium">No radiology records found</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    @if(isset($radiologies) && $radiologies->hasPages())
                        <div class="mt-6 px-2">
                            {{ $radiologies->links() }}
                        </div>
                    @endif
                </div>
                @endif

                {{-- ═══ Orthodontics Tab ═══ --}}
                @if($activeTab == 'orthodontics')
                <div class="block">
                    <x-search-bar :action="route('patients.show', $patient->id)" placeholder="Search by case type..." :extraParams="['tab' => 'orthodontics']">
                        <div class="flex items-center gap-2 flex-nowrap">
                            <select name="status" @change="$refs.form.submit()" 
                                    class="block w-full sm:w-40 py-2 pl-3 pr-10 border border-gray-200 rounded-xl text-sm focus:ring-theme-from focus:border-theme-from font-medium bg-white flex-shrink-0">
                                <option value="">All Statuses</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                    </x-search-bar>

                    <div class="mt-4">
                        <table class="min-w-full rounded-lg overflow-hidden">
                        <thead>
                            <tr class="bg-gray-50/70">
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">#</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Case Type</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Start Date</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($orthodonticCases as $case)
                                <tr class="group hover:bg-theme-from/5 transition-colors duration-150">
                                    <td class="px-6 py-3 font-semibold text-gray-300">{{ ($orthodonticCases->currentPage() - 1) * $orthodonticCases->perPage() + $loop->iteration }}</td>
                                    <td class="px-6 py-3">
                                        <span class="text-sm font-medium text-gray-800">{{ $case->case_type ?? '—' }}</span>
                                    </td>
                                    <td class="px-6 py-3">
                                        <span class="text-sm text-gray-600">{{ $case->start_date?->format('M d, Y') ?? $case->created_at?->format('M d, Y') ?? '—' }}</span>
                                    </td>
                                    <td class="px-6 py-3">
                                        @php
                                            $caseStatusColors = [
                                                'active'    => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                                                'completed' => 'bg-blue-50 text-blue-600 border-blue-100',
                                                'cancelled' => 'bg-red-50 text-red-600 border-red-100',
                                            ];
                                            $caseColor = $caseStatusColors[$case->status ?? ''] ?? 'bg-gray-50 text-gray-600 border-gray-100';
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border {{ $caseColor }}">
                                            {{ ucfirst($case->status ?? '—') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-3 text-right">
                                        <a href="{{ route('orthodontics.cases.show', $case->id) }}" class="inline-flex items-center gap-1 text-theme-from hover:opacity-80 font-semibold text-xs px-2.5 py-1 rounded-lg bg-theme-from/10 border border-theme-from/20 transition-colors duration-150">View</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-10 text-center">
                                        <div class="flex flex-col items-center gap-2">
                                            <svg class="w-10 h-10 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            <p class="text-sm text-gray-400 font-medium">No orthodontic cases found</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    @if(isset($orthodonticCases) && $orthodonticCases->hasPages())
                        <div class="mt-6 px-2">
                            {{ $orthodonticCases->links() }}
                        </div>
                    @endif
                </div>
                @endif

            </div>
        </div>

    </div>
</x-app-layout>
