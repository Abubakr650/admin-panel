<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
        <div class="flex items-center justify-between w-full">
            {{-- Left: Back Button + Breadcrumb + Context --}}
            <div class="flex items-center gap-3">
                <x-button-back href="{{ route('appointments.index') }}" label="Appointments" />
                <div class="h-8 w-px bg-gray-200"></div>
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wider font-medium">Clinic › Appointments</p>
                    <h1 class="text-xl font-bold text-gray-800">Appointment Details</h1>
                </div>
            </div>
            {{-- Right: Action Buttons --}}
            <div class="flex items-center gap-2">
                <x-button-link href="{{ route('appointments.edit', $appointment->id) }}" class="bg-amber-100 text-amber-700 hover:bg-amber-200 border-amber-200">
                    <x-icon name="edit" class="w-4 h-4 mr-1.5" />
                    Edit
                </x-button-link>
                <form action="{{ route('appointments.destroy', $appointment) }}" method="POST"
                      onsubmit="return confirm('Archive this appointment?')">
                    @csrf @method('DELETE')
                    <x-button-archive>Archive</x-button-archive>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        {{-- ── Main Wrapper Card ─────────────────────────────────────── --}}
        <div class="max-w-4xl mx-auto bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">

            {{-- ── Appointment Header ────────────────────────────── --}}
            <div class="bg-gradient-to-r from-theme-from to-theme-to px-8 py-8">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div class="flex items-center gap-5">
                        <div class="w-16 h-16 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center shadow-inner">
                            <x-icon name="calendar" class="w-8 h-8 text-white" />
                        </div>
                        <div>
                            <h2 class="text-white text-2xl font-bold">
                                {{ $appointment->appointment_date?->format('F d, Y') }}
                            </h2>
                            <div class="flex items-center gap-3 mt-1.5">
                                <span class="flex items-center gap-1.5 text-sky-100 text-sm font-medium">
                                    <x-icon name="clock" class="w-4 h-4" />
                                    {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}
                                </span>
                                <div class="w-1 h-1 rounded-full bg-white/30"></div>
                                @php
                                    $statusColors = [
                                        'scheduled'  => 'bg-white/20 text-white border-white/20',
                                        'completed'  => 'bg-emerald-400/20 text-emerald-100 border-emerald-400/30',
                                        'cancelled'  => 'bg-red-400/20 text-red-100 border-red-400/30',
                                        'no_show'    => 'bg-amber-400/20 text-amber-100 border-amber-400/30',
                                    ];
                                    $color = $statusColors[$appointment->appointment_status] ?? 'bg-white/10 text-white border-white/10';
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold border backdrop-blur-sm {{ $color }}">
                                    {{ ucfirst(str_replace('_', ' ', $appointment->appointment_status)) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Main Content Grid ────────────────────────────────── --}}
            <div class="px-8 py-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    
                    {{-- Left Column: Participants --}}
                    <div class="space-y-6">
                        {{-- Patient Card --}}
                        <x-card-link 
                            href="{{ route('patients.show', $appointment->patient_id) }}"
                            icon="user"
                            label="Patient Information"
                            title="{{ $appointment->patient->full_name ?? 'N/A' }}"
                            subtitle="{{ $appointment->patient->phone ?? 'No phone' }}"
                        />

                        {{-- Doctor Card --}}
                        <x-card-link 
                            href="{{ route('doctors.show', $appointment->doctor_id) }}"
                            icon="users"
                            label="Assigned Doctor"
                            title="Dr. {{ $appointment->doctor->user->name ?? 'N/A' }}"
                            subtitle="{{ $appointment->doctor->specialty ?? 'Specialist' }}"
                            color="theme-to"
                        />
                    </div>

                    {{-- Right Column: Notes & Metadata --}}
                    <div class="space-y-6">
                        {{-- Notes --}}
                        <div>
                            <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-4">Appointment Notes</h3>
                            <div class="p-5 rounded-2xl bg-amber-50/50 border border-amber-100 min-h-[140px]">
                                @if($appointment->appointment_notes)
                                    <p class="text-gray-700 text-sm leading-relaxed whitespace-pre-line">
                                        {{ $appointment->appointment_notes }}
                                    </p>
                                @else
                                    <div class="flex flex-col items-center justify-center h-full text-gray-400 py-4">
                                        <x-icon name="document" class="w-8 h-8 opacity-20 mb-2" />
                                        <p class="text-xs italic">No notes added for this appointment</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- System Info --}}
                        <div class="grid grid-cols-2 gap-4">
                            <div class="p-3.5 rounded-xl bg-gray-50 border border-gray-100">
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Created</p>
                                <p class="text-xs font-medium text-gray-700 mt-0.5">{{ $appointment->created_at->format('M d, Y') }}</p>
                            </div>
                            <div class="p-3.5 rounded-xl bg-gray-50 border border-gray-100">
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Last Update</p>
                                <p class="text-xs font-medium text-gray-700 mt-0.5">{{ $appointment->updated_at->format('M d, Y') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── Treatments for this Appointment ────────────────── --}}
                <div class="mt-10 pt-8 border-t border-gray-100">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest">Treatments</h3>
                        @if(!$appointment->trashed() && $appointment->appointment_status === 'completed')
                            <a href="{{ route('treatments.create', ['patient_id' => $appointment->patient_id, 'appointment_id' => $appointment->id]) }}"
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-theme-from/10 text-theme-from text-xs font-semibold border border-theme-from/20 hover:bg-theme-from hover:text-white transition-all duration-200">
                                <x-icon name="plus" class="w-3.5 h-3.5" />
                                Add Treatment
                            </a>
                        @endif
                    </div>

                    @if($appointment->treatments && $appointment->treatments->count() > 0)
                        <div class="rounded-xl border border-gray-100 overflow-hidden">
                            <table class="min-w-full">
                                <thead>
                                    <tr class="bg-gray-50/70">
                                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Type</th>
                                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Service/Item</th>
                                        <th class="px-4 py-2 text-center text-xs font-semibold text-gray-500 uppercase">Qty</th>
                                        <th class="px-4 py-2 text-right text-xs font-semibold text-gray-500 uppercase">Total</th>
                                        <th class="px-4 py-2 text-center text-xs font-semibold text-gray-500 uppercase">Billing</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @php $treatmentsTotal = 0; @endphp
                                    @foreach($appointment->treatments as $t)
                                        @php $treatmentsTotal += $t->total; @endphp
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-2.5">
                                                @php
                                                    $tc = [
                                                        'consultation' => 'bg-indigo-50 text-indigo-600 border-indigo-100',
                                                        'filling' => 'bg-blue-50 text-blue-600 border-blue-100',
                                                        'extraction' => 'bg-red-50 text-red-600 border-red-100',
                                                        'cleaning' => 'bg-teal-50 text-teal-600 border-teal-100',
                                                        'pharmacy_dispense' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                                                    ];
                                                @endphp
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $tc[$t->type] ?? 'bg-gray-50 text-gray-600 border-gray-100' }}">
                                                    {{ ucfirst(str_replace('_', ' ', $t->type)) }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-2.5 text-sm text-gray-700">
                                                {{ $t->service->name ?? $t->description ?? '—' }}
                                            </td>
                                            <td class="px-4 py-2.5 text-center text-sm text-gray-600">{{ $t->quantity }}</td>
                                            <td class="px-4 py-2.5 text-right text-sm font-bold text-gray-800">${{ number_format($t->total, 2) }}</td>
                                            <td class="px-4 py-2.5 text-center">
                                                @php
                                                    $bc = ['pending' => 'bg-amber-50 text-amber-600', 'billed' => 'bg-green-50 text-green-600'];
                                                @endphp
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold {{ $bc[$t->billing_status] ?? 'bg-gray-50 text-gray-600' }}">
                                                    {{ ucfirst($t->billing_status) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="bg-gray-50 border-t border-gray-200">
                                        <td colspan="3" class="px-4 py-3 text-right text-sm font-bold text-gray-600">Total:</td>
                                        <td class="px-4 py-3 text-right text-lg font-extrabold text-theme-from">${{ number_format($treatmentsTotal, 2) }}</td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        {{-- Create Invoice Button (only if there are pending treatments) --}}
                        @if($appointment->treatments->where('billing_status', 'pending')->count() > 0)
                            <div class="mt-4 flex justify-end">
                                <a href="{{ route('invoices.create', ['appointment_id' => $appointment->id]) }}"
                                   class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-theme-from to-theme-to text-white text-sm font-bold shadow-lg hover:shadow-xl hover:-translate-y-0.5 active:scale-95 transition-all">
                                    <x-icon name="document" class="w-4 h-4" />
                                    Create Invoice from Treatments
                                </a>
                            </div>
                        @endif
                    @else
                        <p class="text-xs text-gray-400 italic">
                            @if($appointment->appointment_status === 'completed')
                                No treatments recorded yet. Click "Add Treatment" to begin.
                            @else
                                Complete the appointment first to add treatments.
                            @endif
                        </p>
                    @endif
                </div>

                {{-- Related Appointments (Returns/Parent) --}}
                <div class="mt-10 pt-8 border-t border-gray-100">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest">Related Appointments</h3>
                        {{-- Schedule Follow-up Button --}}
                        @if(!$appointment->trashed())
                            <a href="{{ route('appointments.create', [
                                    'patient_id'            => $appointment->patient_id,
                                    'parent_appointment_id' => $appointment->id,
                                ]) }}"
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-theme-from/10 text-theme-from text-xs font-semibold border border-theme-from/20 hover:bg-theme-from hover:text-white transition-all duration-200">
                                <x-icon name="plus" class="w-3.5 h-3.5" />
                                Schedule Follow-up
                            </a>
                        @endif
                    </div>

                    @if($appointment->parent || $appointment->returns->count() > 0)
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">

                            {{-- Parent Appointment --}}
                            @if($appointment->parent)
                                @php
                                    $parentStatus = $appointment->parent->appointment_status;
                                    $parentColors = ['scheduled' => 'bg-blue-50 text-blue-600 border-blue-100', 'completed' => 'bg-green-50 text-green-600 border-green-100', 'cancelled' => 'bg-red-50 text-red-600 border-red-100', 'no_show' => 'bg-amber-50 text-amber-600 border-amber-100'];
                                    $parentColor  = $parentColors[$parentStatus] ?? 'bg-gray-50 text-gray-600 border-gray-100';
                                @endphp
                                <x-card-link 
                                    href="{{ route('appointments.show', $appointment->parent_appointment_id) }}"
                                    icon="calendar"
                                    size="sm"
                                    label="Original Visit"
                                    title="{{ $appointment->parent->appointment_date?->format('M d, Y') }}"
                                >
                                    <p class="text-[10px] text-gray-400 truncate">
                                        Dr. {{ $appointment->parent->doctor->user->name ?? '—' }}
                                        &nbsp;·&nbsp;
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[9px] font-bold border {{ $parentColor }}">
                                            {{ ucfirst(str_replace('_', ' ', $parentStatus)) }}
                                        </span>
                                    </p>
                                </x-card-link>
                            @endif

                            {{-- Return Visits --}}
                            @foreach($appointment->returns as $return)
                                @php
                                    $retStatus = $return->appointment_status;
                                    $retColors = ['scheduled' => 'bg-blue-50 text-blue-600 border-blue-100', 'completed' => 'bg-green-50 text-green-600 border-green-100', 'cancelled' => 'bg-red-50 text-red-600 border-red-100', 'no_show' => 'bg-amber-50 text-amber-600 border-amber-100'];
                                    $retColor  = $retColors[$retStatus] ?? 'bg-gray-50 text-gray-600 border-gray-100';
                                @endphp
                                <x-card-link 
                                    href="{{ route('appointments.show', $return->id) }}"
                                    icon="arrow-down"
                                    size="sm"
                                    label="Return Visit"
                                    title="{{ $return->appointment_date?->format('M d, Y') }}"
                                >
                                    <p class="text-[10px] text-gray-400 truncate">
                                        Dr. {{ $return->doctor->user->name ?? '—' }}
                                        &nbsp;·&nbsp;
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[9px] font-bold border {{ $retColor }}">
                                            {{ ucfirst(str_replace('_', ' ', $retStatus)) }}
                                        </span>
                                    </p>
                                </x-card-link>
                            @endforeach
                        </div>
                    @else
                        <p class="text-xs text-gray-400 italic">No related appointments yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
