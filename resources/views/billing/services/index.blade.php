<x-app-layout>
@php $isArchived = request()->input('archived') === 'true'; @endphp
    <x-slot name="header">
        <div class="flex items-center justify-between w-full gap-4">
            <div class="flex items-center gap-3 flex-1 min-w-0">
                <div class="shrink-0">
                    <p class="text-xs text-gray-400 uppercase tracking-widest font-semibold">Billing</p>
                    <h1 class="text-lg font-bold text-gray-800 leading-tight">{{ $isArchived ? 'Archived Services' : 'Services Catalog' }}</h1>
                </div>
                <div class="h-8 w-px bg-gray-200 shrink-0"></div>
                <div class="w-64">
                    <x-search-bar action="{{ route('services.index') }}" placeholder="Search service name or code…"
                        :extraParams="array_filter(['archived' => request('archived')])" />
                </div>
            </div>
            <div class="flex items-center gap-3 shrink-0">
                @unless($isArchived)
                    <x-button-link href="{{ route('services.create') }}">
                        <x-icon name="plus" class="w-4 h-4" /> New Service
                    </x-button-link>
                @endunless
                <x-button-archive-view
                    :archived="$isArchived"
                    :indexRoute="route('services.index')"
                    :archiveRoute="route('services.index', ['archived' => 'true'])"
                    :archivedCount="$archivedCount ?? 0"
                />
            </div>
        </div>
    </x-slot>

    <div class="space-y-4">
        <div class="rounded-2xl bg-white border border-gray-100 shadow-sm overflow-hidden">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-gray-50/70">
                        <th class="px-6 py-3 text-left text-md leading-4 font-semibold text-gray-500 uppercase tracking-wider">#</th>
                        <th class="px-6 py-3 text-left text-md leading-4 font-semibold text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-md leading-4 font-semibold text-gray-500 uppercase tracking-wider">Code</th>
                        <th class="px-6 py-3 text-right text-md leading-4 font-semibold text-gray-500 uppercase tracking-wider">Default Price</th>
                        <th class="px-6 py-3 text-center text-md leading-4 font-semibold text-gray-500 uppercase tracking-wider">Duration</th>
                        <th class="px-6 py-3 text-center text-md leading-4 font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-right text-md leading-4 font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($services as $service)
                        <tr class="group hover:bg-theme-from/10 transition-colors duration-150">
                            <td class="px-5 py-3.5 font-semibold text-gray-300">{{ $loop->iteration }}</td>
                            <td class="px-5 py-3.5">
                                <a href="{{ route('services.show', $service->id) }}" class="font-bold text-gray-800 hover:text-theme-from transition-colors">
                                    {{ $service->name }}
                                </a>
                            </td>
                            <td class="px-5 py-3.5">
                                @if($service->code)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-mono font-bold bg-gray-100 text-gray-600">{{ $service->code }}</span>
                                @else
                                    <span class="text-gray-300">—</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-right font-bold text-gray-800">${{ number_format($service->default_price, 2) }}</td>
                            <td class="px-5 py-3.5 text-center text-sm text-gray-600">
                                {{ $service->duration_minutes ? $service->duration_minutes . ' min' : '—' }}
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border {{ $service->is_active ? 'bg-green-50 text-green-600 border-green-100' : 'bg-gray-50 text-gray-500 border-gray-100' }}">
                                    {{ $service->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <x-table-actions
                                :archived="$isArchived"
                                :editRoute="route('services.edit', $service->id)"
                                :deleteRoute="route('services.destroy', $service->id)"
                                :restoreRoute="route('services.restore', $service->id)"
                                :itemName="$service->name"
                            />
                        </tr>
                    @empty
                        <x-message-empty colspan="7">
                            {{ $isArchived ? 'No archived services' : 'No services found. Click "New Service" to add one.' }}
                        </x-message-empty>
                    @endforelse
                </tbody>
            </table>
            @if($services->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">{{ $services->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>
