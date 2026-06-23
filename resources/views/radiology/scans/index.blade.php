<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between w-full">
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-widest font-semibold">Radiology</p>
                <h1 class="text-xl font-bold text-gray-800">Radiology Scans</h1>
            </div>
            <a href="{{ route('radiology.scans.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-theme-from to-theme-to text-white text-sm font-semibold rounded-xl hover:shadow-lg hover:shadow-theme-from/20 transition-all duration-200">
                <x-icon name="plus" class="w-4 h-4" />
                New Scan & Analysis
            </a>
        </div>
    </x-slot>

    <div class="p-6">
        @if(session('success'))
            <div class="mb-4 p-4 bg-green-50 border border-green-200 flex items-start gap-3 rounded-xl">
                <x-icon name="check-circle" class="w-5 h-5 text-green-500 shrink-0 mt-0.5" />
                <div class="text-sm text-green-700 font-medium">
                    {{ session('success') }}
                </div>
            </div>
        @endif

        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50/50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Patient</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Doctor</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Has Analysis</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($scans as $scan)
                            <tr class="hover:bg-gray-50/80 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-theme-from/10 flex items-center justify-center shrink-0">
                                            <x-icon name="user" class="w-4 h-4 text-theme-from" />
                                        </div>
                                        <span class="text-sm font-bold text-gray-800">{{ $scan->patient->full_name ?? $scan->patient->name ?? '—' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm text-gray-600 font-medium">Dr. {{ $scan->doctor->user->name ?? '—' }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-gray-100 text-gray-800 border border-gray-200">
                                        {{ $scan->radiology_type ?? '—' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($scan->ai_analysis)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-green-50 text-green-700 border border-green-200">
                                            <x-icon name="check-circle" class="w-3.5 h-3.5" />
                                            Yes
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-medium bg-gray-50 text-gray-500 border border-gray-200">
                                            No
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm text-gray-500">{{ $scan->created_at->format('M j, Y') }}</span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('radiology.scans.show', $scan) }}"
                                           class="p-2 text-theme-from hover:bg-theme-from/10 rounded-lg transition-colors" title="View Details">
                                            <x-icon name="eye" class="w-4 h-4" />
                                        </a>
                                        <a href="{{ route('radiology.scans.edit', $scan) }}"
                                           class="p-2 text-amber-500 hover:bg-amber-50 rounded-lg transition-colors" title="Edit Scan">
                                            <x-icon name="pencil" class="w-4 h-4" />
                                        </a>
                                        <form action="{{ route('radiology.scans.destroy', $scan) }}" method="POST"
                                              onsubmit="return confirm('Are you sure you want to delete this scan?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition-colors" title="Delete Scan">
                                                <x-icon name="trash" class="w-4 h-4" />
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-3 border border-gray-100">
                                            <x-icon name="photograph" class="w-8 h-8 text-gray-300" />
                                        </div>
                                        <h3 class="text-sm font-bold text-gray-800">No scans found</h3>
                                        <p class="text-xs text-gray-500 mt-1">Get started by creating a new radiology scan.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($scans->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/30">
                    {{ $scans->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
