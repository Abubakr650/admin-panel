<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between w-full gap-4">
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-widest font-semibold">Billing › Services</p>
                <h1 class="text-xl font-bold text-gray-800">{{ $service->name }}</h1>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('services.edit', $service->id) }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 rounded-xl text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-all shadow-sm">
                    <x-icon name="pencil" class="w-4 h-4 text-theme-from" /> Edit
                </a>
                <form action="{{ route('services.destroy', $service->id) }}" method="POST" onsubmit="return confirm('Archive this service?')">
                    @csrf @method('DELETE')
                    <x-button-archive>Archive</x-button-archive>
                </form>
            </div>
        </div>
    </x-slot>
    <div class="p-6">
        <div class="max-w-2xl mx-auto space-y-5">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-theme-from to-theme-to px-8 py-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-white text-xl font-bold">{{ $service->name }}</h2>
                            @if($service->code)
                                <span class="inline-flex items-center px-2.5 py-1 mt-1.5 rounded-full text-xs font-mono font-bold bg-white/20 text-white">{{ $service->code }}</span>
                            @endif
                        </div>
                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-bold border {{ $service->is_active ? 'bg-green-50 text-green-600 border-green-100' : 'bg-gray-50 text-gray-500 border-gray-100' }}">
                            {{ $service->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                </div>
                <div class="px-8 py-6 grid grid-cols-2 gap-6">
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Default Price</p>
                        <p class="text-2xl font-extrabold text-theme-from">${{ number_format($service->default_price, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Duration</p>
                        <p class="font-bold text-gray-800">{{ $service->duration_minutes ? $service->duration_minutes . ' minutes' : '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Treatments Linked</p>
                        <p class="font-bold text-gray-800">{{ $service->treatments()->count() }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Created</p>
                        <p class="font-semibold text-gray-800">{{ $service->created_at->format('M d, Y') }}</p>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between">
                <a href="{{ route('services.index') }}" class="text-sm text-theme-from hover:underline flex items-center gap-1">
                    <x-icon name="arrow-left" class="w-4 h-4" /> Back to Services
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
