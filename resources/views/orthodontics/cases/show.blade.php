<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between w-full">
            {{-- Left: Back Button + Breadcrumb + Patient Name --}}
            <div class="flex items-center gap-3">
                <x-button-back href="{{ route('orthodontics.cases.index') }}" label="Cases" />
                <div class="h-8 w-px bg-gray-200"></div>
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wider font-medium">Orthodontics › Cases</p>
                    <h1 class="text-xl font-bold text-gray-800">Case: {{ $case->patient->full_name ?? $case->patient->name ?? 'Unknown' }}</h1>
                </div>
            </div>
            {{-- Right: Action Buttons --}}
            <div class="flex items-center gap-2">
                <x-button-link href="{{ route('orthodontics.cases.edit', $case) }}">
                    Edit Case
                </x-button-link>
            </div>
        </div>
    </x-slot>

    <div class="space-y-5">
        {{-- ── Main Wrapper Card ─────────────────────────────────────── --}}
        <div class="max-w-full mx-auto bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">

            {{-- ── Case Header ────────────────────────────── --}}
            <div class="bg-gradient-to-r from-theme-from to-theme-to px-8 py-6">
                <div class="flex items-center gap-5">
                    {{-- Icon --}}
                    <div class="w-16 h-16 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center shadow-inner">
                        <x-icon name="clipboard-list" class="w-8 h-8 text-white" />
                    </div>
                    <div class="flex-1">
                        <h2 class="text-white text-2xl font-bold">Orthodontic Case</h2>
                        <div class="flex items-center gap-3 mt-1.5">
                            {{-- Status Badge --}}
                            @php
                                $statusColors = [
                                    'active'    => 'bg-emerald-500/20 text-emerald-100 border-emerald-500/30',
                                    'completed' => 'bg-blue-500/20 text-blue-100 border-blue-500/30',
                                    'on_hold'   => 'bg-amber-500/20 text-amber-100 border-amber-500/30',
                                ];
                                $color = $statusColors[$case->status ?? ''] ?? 'bg-white/20 text-white border-white/10';
                            @endphp
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold backdrop-blur-sm border {{ $color }}">
                                {{ ucfirst(str_replace('_', ' ', $case->status ?? 'Unknown')) }}
                            </span>
                            <span class="text-sky-100 text-sm">Patient: {{ $case->patient->full_name ?? $case->patient->name ?? '—' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Case Info Cards ────────────────────────────────── --}}
            <div class="px-8 py-6 border-b border-gray-100">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    {{-- Doctor --}}
                    <div class="flex items-center gap-3 p-3.5 rounded-xl bg-gray-50/80 border border-gray-100">
                        <div class="flex-shrink-0 flex items-center justify-center w-10 h-10 rounded-xl bg-theme-from/10">
                            <x-icon name="user" class="w-5 h-5 text-theme-from" />
                        </div>
                        <div class="min-w-0">
                            <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Doctor</p>
                            <p class="text-sm font-medium text-gray-800 truncate">Dr. {{ $case->doctor->user->name ?? '—' }}</p>
                        </div>
                    </div>
                    {{-- Total Amount --}}
                    <div class="flex items-center gap-3 p-3.5 rounded-xl bg-gray-50/80 border border-gray-100">
                        <div class="flex-shrink-0 flex items-center justify-center w-10 h-10 rounded-xl bg-theme-from/10">
                            <x-icon name="currency-dollar" class="w-5 h-5 text-theme-from" />
                        </div>
                        <div class="min-w-0">
                            <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Total Amount</p>
                            <p class="text-sm font-medium text-gray-800 truncate">{{ number_format($case->total_amount, 2) }}</p>
                        </div>
                    </div>
                    {{-- Installment --}}
                    <div class="flex items-center gap-3 p-3.5 rounded-xl bg-gray-50/80 border border-gray-100">
                        <div class="flex-shrink-0 flex items-center justify-center w-10 h-10 rounded-xl bg-theme-to/10">
                            <x-icon name="currency-dollar" class="w-5 h-5 text-theme-to" />
                        </div>
                        <div class="min-w-0">
                            <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Installment</p>
                            <p class="text-sm font-medium text-gray-800 truncate">{{ number_format($case->installment_amount, 2) }}</p>
                        </div>
                    </div>
                    {{-- Started --}}
                    <div class="flex items-center gap-3 p-3.5 rounded-xl bg-gray-50/80 border border-gray-100">
                        <div class="flex-shrink-0 flex items-center justify-center w-10 h-10 rounded-xl bg-theme-from/10">
                            <x-icon name="calendar" class="w-5 h-5 text-theme-from" />
                        </div>
                        <div class="min-w-0">
                            <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Started</p>
                            <p class="text-sm font-medium text-gray-800 truncate">{{ $case->created_at->format('M d, Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 p-6">
                <div class="lg:col-span-2 space-y-6">
                    {{-- Diagnosis / Plan --}}
                    <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm space-y-6">
                        <div>
                            <h3 class="flex items-center gap-2 text-sm font-bold text-gray-700 uppercase tracking-wider mb-3">
                                <x-icon name="document-text" class="w-4 h-4 text-theme-from" />
                                Diagnosis
                            </h3>
                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-100 text-sm text-gray-600 leading-relaxed">
                                {{ $case->diagnosis ?: 'No diagnosis recorded.' }}
                            </div>
                        </div>
                        <div>
                            <h3 class="flex items-center gap-2 text-sm font-bold text-gray-700 uppercase tracking-wider mb-3">
                                <x-icon name="clipboard-list" class="w-4 h-4 text-theme-from" />
                                Treatment Plan
                            </h3>
                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-100 text-sm text-gray-600 leading-relaxed whitespace-pre-wrap">{{ $case->plan ?: 'No plan recorded.' }}</div>
                        </div>
                    </div>

                    {{-- Sessions History --}}
                    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
                        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
                            <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wider flex items-center gap-2">
                                <x-icon name="calendar" class="w-4 h-4 text-theme-from" />
                                Sessions History
                            </h3>
                            <x-button-link href="{{ route('orthodontics.sessions.create', ['case_id' => $case->id]) }}" class="!py-1.5 !px-3 !text-xs">
                                + Log Session
                            </x-button-link>
                        </div>
                        <table class="min-w-full divide-y divide-gray-100">
                            <thead class="bg-gray-50/50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-[11px] font-bold text-gray-400 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-[11px] font-bold text-gray-400 uppercase tracking-wider">Procedure</th>
                                    <th class="px-6 py-3 text-right text-[11px] font-bold text-gray-400 uppercase tracking-wider">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($case->sessions as $session)
                                    <tr class="hover:bg-theme-from/5 transition-colors duration-150 group">
                                        <td class="px-6 py-3.5 text-sm text-gray-600">{{ $session->session_date }}</td>
                                        <td class="px-6 py-3.5 text-sm text-gray-800 font-medium">{{ Str::limit($session->treatment, 50) }}</td>
                                        <td class="px-6 py-3.5 text-right">
                                            <a href="{{ route('orthodontics.sessions.show', $session) }}" class="inline-flex items-center gap-1 text-theme-from hover:opacity-80 font-semibold text-xs px-2.5 py-1 rounded-lg bg-theme-from/10 border border-theme-from/20 transition-colors duration-150 opacity-0 group-hover:opacity-100">View</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-6 py-10 text-center">
                                            <div class="flex flex-col items-center gap-2">
                                                <x-icon name="calendar" class="w-10 h-10 text-gray-200" />
                                                <p class="text-sm text-gray-400 font-medium">No sessions recorded yet</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Sidebar --}}
                <div class="space-y-6">
                    <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm relative overflow-hidden">
                        {{-- Background Pattern --}}
                        <div class="absolute -right-6 -top-6 text-gray-50 opacity-50 transform rotate-12">
                            <x-icon name="currency-dollar" class="w-32 h-32" />
                        </div>
                        
                        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4 relative z-10">Financial Summary</h3>
                        
                        <div class="space-y-4 relative z-10">
                            <div class="flex justify-between items-center p-3 rounded-lg bg-gray-50 border border-gray-100">
                                <span class="text-sm font-medium text-gray-500">Total Paid</span>
                                <span class="font-bold text-emerald-600">0.00</span>
                            </div>
                            <div class="flex justify-between items-center p-3 rounded-lg bg-red-50 border border-red-100">
                                <span class="text-sm font-medium text-red-600">Remaining Balance</span>
                                <span class="font-bold text-red-700">{{ number_format($case->total_amount, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
