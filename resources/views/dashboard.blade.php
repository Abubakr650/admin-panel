<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between w-full gap-4">
            <div class="flex items-center gap-3 flex-1 min-w-0">
                <div class="shrink-0">
                    <p class="text-xs text-gray-400 uppercase tracking-widest font-semibold">Overview</p>
                    <h1 class="text-lg font-bold text-gray-800 leading-tight">
                        Dashboard
                    </h1>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">

        {{-- ── Stats Grid ─────────────────────────────────────────────── --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Total Patients --}}
            <div class="relative overflow-hidden rounded-2xl bg-white border border-gray-100 shadow-sm p-4 flex items-center gap-4">
                <div class="flex-shrink-0 flex items-center justify-center w-11 h-11 rounded-xl bg-theme-from/10">
                    <x-icon name="users" class="w-5 h-5 text-theme-from" />
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total Patients</p>
                    <p class="text-2xl font-extrabold text-gray-800">{{ number_format($stats['total_patients']) }}</p>
                </div>
                <div class="absolute -right-3 -bottom-3 w-16 h-16 rounded-full bg-theme-from/10 opacity-60"></div>
            </div>

            {{-- Today's Appointments --}}
            <div class="relative overflow-hidden rounded-2xl bg-white border border-gray-100 shadow-sm p-4 flex items-center gap-4">
                <div class="flex-shrink-0 flex items-center justify-center w-11 h-11 rounded-xl bg-green-50">
                    <x-icon name="calendar" class="w-5 h-5 text-green-500" />
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Appointments Today</p>
                    <p class="text-2xl font-extrabold text-gray-800">{{ number_format($stats['today_appointments']) }}</p>
                </div>
                <div class="absolute -right-3 -bottom-3 w-16 h-16 rounded-full bg-green-50 opacity-60"></div>
            </div>

            {{-- Total Revenue --}}
            <div class="relative overflow-hidden rounded-2xl bg-white border border-gray-100 shadow-sm p-4 flex items-center gap-4">
                <div class="flex-shrink-0 flex items-center justify-center w-11 h-11 rounded-xl bg-amber-50">
                    <x-icon name="currency-dollar" class="w-5 h-5 text-amber-500" />
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Revenue</p>
                    <p class="text-2xl font-extrabold text-gray-800">
                        {{ number_format($stats['total_revenue'], 2) }} <span class="text-sm font-semibold text-gray-400">IQD</span>
                    </p>
                </div>
                <div class="absolute -right-3 -bottom-3 w-16 h-16 rounded-full bg-amber-50 opacity-60"></div>
            </div>

            {{-- Total Doctors --}}
            <div class="relative overflow-hidden rounded-2xl bg-white border border-gray-100 shadow-sm p-4 flex items-center gap-4">
                <div class="flex-shrink-0 flex items-center justify-center w-11 h-11 rounded-xl bg-blue-50">
                    <x-icon name="user" class="w-5 h-5 text-blue-500" />
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Doctors</p>
                    <p class="text-2xl font-extrabold text-gray-800">{{ number_format($stats['total_doctors']) }}</p>
                </div>
                <div class="absolute -right-3 -bottom-3 w-16 h-16 rounded-full bg-blue-50 opacity-60"></div>
            </div>
        </div>

        {{-- ── Main Content Grid ──────────────────────────────────────── --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            {{-- Left Column (Wider): Chart & Recent Invoices --}}
            <div class="lg:col-span-2 space-y-6">
                
                {{-- Chart Section --}}
                <div class="rounded-2xl bg-white border border-gray-100 shadow-sm p-6 relative overflow-hidden">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-gray-800">Appointments (Last 7 Days)</h3>
                    </div>
                    <div class="h-64 relative w-full">
                        <canvas id="appointmentsChart"></canvas>
                    </div>
                </div>

                {{-- Recent Invoices --}}
                <div class="rounded-2xl bg-white border border-gray-100 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/70">
                        <h3 class="text-lg font-bold text-gray-800">Recent Invoices</h3>
                        <a href="{{ route('invoices.index') }}" class="text-sm font-semibold text-theme-from hover:text-theme-to transition-colors">View All &rarr;</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr class="bg-gray-50/70">
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Invoice #</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Patient</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Amount</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse($recentInvoices as $invoice)
                                    <tr class="group hover:bg-theme-from/10 transition-colors duration-150">
                                        <td class="px-6 py-3.5 whitespace-nowrap text-sm font-bold text-theme-from">
                                            <a href="{{ route('invoices.show', $invoice->id) }}">#{{ $invoice->invoice_number ?? substr($invoice->id, 0, 8) }}</a>
                                        </td>
                                        <td class="px-6 py-3.5 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="h-8 w-8 rounded-full bg-theme-from/10 flex items-center justify-center text-theme-from font-bold uppercase">
                                                    {{ mb_substr($invoice->patient->name ?? '?', 0, 1) }}
                                                </div>
                                                <div class="ml-3 text-sm font-semibold text-gray-800">
                                                    {{ $invoice->patient->name ?? 'N/A' }}
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-3.5 whitespace-nowrap text-sm font-extrabold text-gray-800">
                                            {{ number_format($invoice->total_amount ?? 0, 2) }}
                                        </td>
                                        <td class="px-6 py-3.5 whitespace-nowrap text-sm">
                                            @if(isset($invoice->status) && $invoice->status === 'paid')
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-green-50 text-green-600 border border-green-100">
                                                    Paid
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-600 border border-amber-100">
                                                    Unpaid
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-8 text-center text-gray-500 font-semibold">
                                            No recent invoices found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Right Column: Today's Appointments & Recent Patients --}}
            <div class="space-y-6">
                
                {{-- Today's Appointments --}}
                <div class="rounded-2xl bg-white border border-gray-100 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/70">
                        <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                            <span class="relative flex h-3 w-3">
                              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                              <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                            </span>
                            Appointments Today
                        </h3>
                    </div>
                    <div class="p-0">
                        <ul class="divide-y divide-gray-100">
                            @forelse($todayAppointments as $appointment)
                                <li class="px-6 py-4 group hover:bg-theme-from/10 transition-colors duration-150">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center min-w-0 gap-3">
                                            <div class="flex-shrink-0">
                                                <div class="h-10 w-10 rounded-xl bg-theme-from/10 flex items-center justify-center text-theme-from font-bold uppercase">
                                                    {{ mb_substr($appointment->patient->name ?? '?', 0, 1) }}
                                                </div>
                                            </div>
                                            <div class="truncate">
                                                <p class="text-sm font-bold text-gray-800 truncate">
                                                    {{ $appointment->patient->name ?? 'N/A' }}
                                                </p>
                                                <p class="text-xs font-semibold text-gray-500 truncate flex items-center gap-1 mt-0.5">
                                                    <x-icon name="user" class="w-3.5 h-3.5 text-gray-400" />
                                                    Dr. {{ $appointment->doctor->name ?? 'N/A' }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="text-right shrink-0">
                                            <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-bold bg-gray-100 text-gray-700">
                                                {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}
                                            </span>
                                        </div>
                                    </div>
                                </li>
                            @empty
                                <li class="px-6 py-8 text-center text-gray-500 font-semibold">
                                    No appointments today.
                                </li>
                            @endforelse
                        </ul>
                    </div>
                    <div class="px-6 py-3 border-t border-gray-100 bg-gray-50/70">
                        <a href="{{ route('appointments.index') }}" class="w-full flex justify-center items-center px-4 py-2 border border-gray-200 shadow-sm text-sm font-bold rounded-xl text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                            Manage Appointments
                        </a>
                    </div>
                </div>

                {{-- Recent Patients --}}
                <div class="rounded-2xl bg-white border border-gray-100 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/70">
                        <h3 class="text-lg font-bold text-gray-800">New Patients</h3>
                    </div>
                    <div class="p-0">
                        <ul class="divide-y divide-gray-100">
                            @forelse($recentPatients as $patient)
                                <li class="px-6 py-4 group hover:bg-theme-from/10 transition-colors duration-150">
                                    <div class="flex items-center justify-between">
                                        <div class="min-w-0 flex-1">
                                            <a href="{{ route('patients.show', $patient->id) }}" class="text-sm font-bold text-gray-800 hover:text-theme-from truncate block transition-colors">
                                                {{ $patient->name }}
                                            </a>
                                            <p class="text-xs font-semibold text-gray-500 mt-0.5" dir="ltr">
                                                {{ $patient->phone ?? 'No Phone' }}
                                            </p>
                                        </div>
                                        <div class="text-right text-xs font-semibold text-gray-400 shrink-0">
                                            {{ $patient->created_at->diffForHumans() }}
                                        </div>
                                    </div>
                                </li>
                            @empty
                                <li class="px-6 py-8 text-center text-gray-500 font-semibold">
                                    No recent patients.
                                </li>
                            @endforelse
                        </ul>
                    </div>
                </div>

            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('appointmentsChart').getContext('2d');
            
            const labels = @json($last7Days);
            const data = @json($appointmentsData);
            
            // Get CSS variable for theme-from if available, otherwise use a default
            const styles = getComputedStyle(document.documentElement);
            const themeFromHex = styles.getPropertyValue('--theme-from').trim() || '#4f46e5'; 
            
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Appointments',
                        data: data,
                        borderColor: '#6366f1', // Fallback color, could use css variables
                        backgroundColor: 'rgba(99, 102, 241, 0.1)',
                        borderWidth: 3,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: '#6366f1',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: '#1f2937',
                            padding: 12,
                            titleFont: {
                                family: 'figtree, sans-serif',
                                size: 14,
                                weight: 'bold'
                            },
                            bodyFont: {
                                family: 'figtree, sans-serif',
                                size: 13,
                                weight: '500'
                            },
                            displayColors: false,
                            cornerRadius: 8,
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1,
                                font: {
                                    family: 'figtree, sans-serif',
                                    weight: '600'
                                }
                            },
                            grid: {
                                color: '#f3f4f6',
                                drawBorder: false,
                            }
                        },
                        x: {
                            grid: {
                                display: false,
                                drawBorder: false,
                            },
                            ticks: {
                                font: {
                                    family: 'figtree, sans-serif',
                                    weight: '600'
                                }
                            }
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index',
                    },
                }
            });
        });
    </script>
    @endpush
</x-app-layout>
