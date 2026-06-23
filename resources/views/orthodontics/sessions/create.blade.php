<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wider font-medium">Orthodontics › Sessions</p>
                <h1 class="text-xl font-bold text-gray-800">Log Orthodontic Session</h1>
            </div>
        </div>
    </x-slot>

    <div class="p-6">
        <div class="max-w-3xl mx-auto">

            {{-- Card --}}
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">

                {{-- Card Header --}}
                <div class="bg-gradient-to-r from-theme-from to-theme-to px-8 py-6">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center shadow-inner">
                            <x-icon name="clipboard-list" class="w-7 h-7 text-white" />
                        </div>
                        <div>
                            <h2 class="text-white text-xl font-bold">Session Registration</h2>
                            <p class="text-sky-100 text-sm mt-0.5">Record a new orthodontic session details</p>
                        </div>
                    </div>
                </div>

                {{-- Card Body --}}
                <div class="px-8 py-7">
                    @php
                        $inputClass = fn(string $field) =>
                            'w-full px-4 py-2.5 rounded-xl border text-gray-800 text-sm placeholder-gray-400 ' .
                            'transition-all duration-200 ' .
                            'focus:outline-none focus:ring-2 focus:ring-theme-from/40 focus:border-theme-from ' .
                            ($errors->has($field)
                                ? 'border-red-400 bg-red-50 ring-1 ring-red-300'
                                : 'border-gray-200 bg-gray-50 hover:border-theme-from');
                    @endphp

                    <form action="{{ route('orthodontics.sessions.store') }}" method="POST" class="space-y-5">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Case --}}
                            <div class="md:col-span-2">
                                <label for="case_id" class="block text-sm font-semibold text-gray-600 mb-1.5">
                                    <span class="flex items-center gap-1.5">
                                        <x-icon name="user" class="w-4 h-4 text-theme-from" />
                                        Orthodontic Case <span class="text-red-400">*</span>
                                    </span>
                                </label>
                                <select id="case_id" name="case_id" required
                                        class="{{ $inputClass('case_id') }}">
                                    <option value="">— Select Case —</option>
                                    @foreach($cases as $case)
                                        <option value="{{ $case->id }}" {{ (old('case_id') == $case->id || request('case_id') == $case->id) ? 'selected' : '' }}>
                                            Case of: {{ $case->patient->full_name ?? $case->patient->name ?? 'Patient' }} (Status: {{ ucfirst($case->status) }})
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('case_id')" class="mt-1.5" />
                            </div>

                            {{-- Session Date --}}
                            <div>
                                <label for="session_date" class="block text-sm font-semibold text-gray-600 mb-1.5">
                                    <span class="flex items-center gap-1.5">
                                        <x-icon name="calendar" class="w-4 h-4 text-theme-from" />
                                        Session Date <span class="text-red-400">*</span>
                                    </span>
                                </label>
                                <input type="date" id="session_date" name="session_date"
                                       value="{{ old('session_date', date('Y-m-d')) }}" required
                                       class="{{ $inputClass('session_date') }}">
                                <x-input-error :messages="$errors->get('session_date')" class="mt-1.5" />
                            </div>

                            {{-- Treatment --}}
                            <div class="md:col-span-2">
                                <label for="treatment" class="block text-sm font-semibold text-gray-600 mb-1.5">
                                    <span class="flex items-center gap-1.5">
                                        <x-icon name="document-text" class="w-4 h-4 text-theme-from" />
                                        Treatment / Procedure Done <span class="text-red-400">*</span>
                                    </span>
                                </label>
                                <input type="text" id="treatment" name="treatment"
                                       value="{{ old('treatment') }}" required placeholder="e.g. Wire changed"
                                       class="{{ $inputClass('treatment') }}">
                                <x-input-error :messages="$errors->get('treatment')" class="mt-1.5" />
                            </div>

                            {{-- Teeth Changes --}}
                            <div>
                                <label for="teeth_changes" class="block text-sm font-semibold text-gray-600 mb-1.5">
                                    <span class="flex items-center gap-1.5">
                                        <x-icon name="document-text" class="w-4 h-4 text-theme-from" />
                                        Teeth Changes
                                    </span>
                                </label>
                                <textarea id="teeth_changes" name="teeth_changes" rows="3"
                                          placeholder="Notes on teeth..."
                                          class="{{ $inputClass('teeth_changes') }} resize-none">{{ old('teeth_changes') }}</textarea>
                                <x-input-error :messages="$errors->get('teeth_changes')" class="mt-1.5" />
                            </div>

                            {{-- Gum Changes --}}
                            <div>
                                <label for="gum_changes" class="block text-sm font-semibold text-gray-600 mb-1.5">
                                    <span class="flex items-center gap-1.5">
                                        <x-icon name="document-text" class="w-4 h-4 text-theme-from" />
                                        Gum Changes
                                    </span>
                                </label>
                                <textarea id="gum_changes" name="gum_changes" rows="3"
                                          placeholder="Notes on gums..."
                                          class="{{ $inputClass('gum_changes') }} resize-none">{{ old('gum_changes') }}</textarea>
                                <x-input-error :messages="$errors->get('gum_changes')" class="mt-1.5" />
                            </div>

                            {{-- Wire Type Upper --}}
                            <div>
                                <label for="wire_type_upper" class="block text-sm font-semibold text-gray-600 mb-1.5">
                                    <span class="flex items-center gap-1.5">
                                        <x-icon name="document-text" class="w-4 h-4 text-theme-from" />
                                        Wire Type (Upper)
                                    </span>
                                </label>
                                <input type="text" id="wire_type_upper" name="wire_type_upper"
                                       value="{{ old('wire_type_upper') }}" placeholder="e.g. 0.014 NiTi"
                                       class="{{ $inputClass('wire_type_upper') }}">
                                <x-input-error :messages="$errors->get('wire_type_upper')" class="mt-1.5" />
                            </div>

                            {{-- Wire Type Lower --}}
                            <div>
                                <label for="wire_type_lower" class="block text-sm font-semibold text-gray-600 mb-1.5">
                                    <span class="flex items-center gap-1.5">
                                        <x-icon name="document-text" class="w-4 h-4 text-theme-from" />
                                        Wire Type (Lower)
                                    </span>
                                </label>
                                <input type="text" id="wire_type_lower" name="wire_type_lower"
                                       value="{{ old('wire_type_lower') }}" placeholder="e.g. 0.014 NiTi"
                                       class="{{ $inputClass('wire_type_lower') }}">
                                <x-input-error :messages="$errors->get('wire_type_lower')" class="mt-1.5" />
                            </div>
                        </div>

                        {{-- Divider --}}
                        <div class="border-t border-dashed border-gray-200 pt-2"></div>

                        {{-- Actions --}}
                        <div class="flex items-center justify-end gap-3">
                            <x-button-cancel/>
                            <x-button-submit>
                                <x-icon name="clipboard-list" class="w-4 h-4 mr-1.5" />
                                Log Session
                            </x-button-submit>
                        </div>

                    </form>
                </div>
            </div>

            {{-- Decorative hint --}}
            <p class="text-center text-xs text-gray-400 mt-4 flex items-center justify-center gap-1">
                <x-icon name="info" class="w-3.5 h-3.5 text-theme-from" />
                Fields marked with <span class="text-red-400 font-medium mx-0.5">*</span> are required
            </p>

        </div>
    </div>
</x-app-layout>
