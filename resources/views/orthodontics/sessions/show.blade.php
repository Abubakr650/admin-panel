<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between w-full">
            {{-- Left: Back Button + Breadcrumb + Info --}}
            <div class="flex items-center gap-3">
                <x-button-back href="{{ route('orthodontics.cases.show', $session->case_id) }}" label="Case" />
                <div class="h-8 w-px bg-gray-200"></div>
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wider font-medium">Orthodontics › Sessions</p>
                    <h1 class="text-xl font-bold text-gray-800">Session Date: {{ $session->session_date }}</h1>
                </div>
            </div>
            {{-- Right: Action Buttons --}}
            <div class="flex items-center gap-2">
                <x-button-link href="{{ route('orthodontics.sessions.edit', $session) }}">
                    Edit Session
                </x-button-link>
            </div>
        </div>
    </x-slot>

    <div class="space-y-5">
        {{-- ── Main Wrapper Card ─────────────────────────────────────── --}}
        <div class="max-w-4xl mx-auto bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">

            {{-- ── Session Header ────────────────────────────── --}}
            <div class="bg-gradient-to-r from-theme-from to-theme-to px-8 py-6">
                <div class="flex items-center gap-5">
                    {{-- Icon --}}
                    <div class="w-16 h-16 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center shadow-inner">
                        <x-icon name="calendar" class="w-8 h-8 text-white" />
                    </div>
                    <div class="flex-1">
                        <h2 class="text-white text-2xl font-bold">Orthodontic Session</h2>
                        <div class="flex items-center gap-3 mt-1.5">
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-white/20 text-white backdrop-blur-sm border border-white/10">
                                <x-icon name="user" class="w-3.5 h-3.5" />
                                Patient: {{ $session->orthodonticCase->patient->full_name ?? $session->orthodonticCase->patient->name ?? '—' }}
                            </span>
                            <span class="text-sky-100 text-sm">Date: {{ \Carbon\Carbon::parse($session->session_date)->format('M d, Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    {{-- Left Column: Primary Details --}}
                    <div class="space-y-6">
                        <div>
                            <h3 class="flex items-center gap-2 text-sm font-bold text-gray-700 uppercase tracking-wider mb-3">
                                <x-icon name="clipboard-list" class="w-4 h-4 text-theme-from" />
                                Treatment Done
                            </h3>
                            <div class="bg-gray-50 rounded-xl p-4 border border-gray-100 text-sm text-gray-800 font-medium leading-relaxed">
                                {{ $session->treatment }}
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="p-4 rounded-xl bg-gray-50/80 border border-gray-100">
                                <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Upper Wire</p>
                                <p class="text-sm font-medium text-gray-800">{{ $session->wire_type_upper ?: '—' }}</p>
                            </div>
                            <div class="p-4 rounded-xl bg-gray-50/80 border border-gray-100">
                                <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Lower Wire</p>
                                <p class="text-sm font-medium text-gray-800">{{ $session->wire_type_lower ?: '—' }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Right Column: Observations --}}
                    <div class="space-y-6">
                        <div>
                            <h3 class="flex items-center gap-2 text-sm font-bold text-gray-700 uppercase tracking-wider mb-3">
                                <x-icon name="document-text" class="w-4 h-4 text-theme-from" />
                                Teeth Observations
                            </h3>
                            <div class="bg-gray-50 rounded-xl p-4 border border-gray-100 text-sm text-gray-600 leading-relaxed min-h-[5rem]">
                                {{ $session->teeth_changes ?: 'No specific teeth observations recorded.' }}
                            </div>
                        </div>

                        <div>
                            <h3 class="flex items-center gap-2 text-sm font-bold text-gray-700 uppercase tracking-wider mb-3">
                                <x-icon name="document-text" class="w-4 h-4 text-theme-from" />
                                Gum Observations
                            </h3>
                            <div class="bg-gray-50 rounded-xl p-4 border border-gray-100 text-sm text-gray-600 leading-relaxed min-h-[5rem]">
                                {{ $session->gum_changes ?: 'No specific gum observations recorded.' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
