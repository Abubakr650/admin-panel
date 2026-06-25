<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between w-full">
            {{-- Left: Back Button + Breadcrumb + Patient Name --}}
            <div class="flex items-center gap-3">
                <x-button-back href="{{ route('radiology.scans.index') }}" label="Scans" />
                <div class="h-8 w-px bg-gray-200"></div>
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wider font-medium">Radiology › Scans</p>
                    <h1 class="text-xl font-bold text-gray-800">{{ $scan->radiology_type }}</h1>
                </div>
            </div>
            
            <div class="flex items-center gap-3">
                <a href="{{ route('radiology.scans.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 text-gray-700 text-sm font-semibold rounded-xl hover:bg-gray-50 transition-colors">
                    <x-icon name="plus" class="w-4 h-4" />
                    New Scan
                </a>
                <button onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-theme-from to-theme-to text-white text-sm font-semibold rounded-xl hover:shadow-lg hover:shadow-theme-from/20 transition-all duration-200">
                    <x-icon name="printer" class="w-4 h-4" />
                    Print Report
                </button>
            </div>
        </div>
    </x-slot>

    <div class="p-6">
        <div class="max-w-full mx-auto space-y-6">

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                {{-- Left Column: Image & Details --}}
                <div class="lg:col-span-5 space-y-6">
                    
                    {{-- Patient Details Card --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                            <h2 class="text-base font-bold text-gray-800 flex items-center gap-2">
                                <x-icon name="user" class="w-5 h-5 text-theme-from" />
                                Scan Information
                            </h2>
                        </div>
                        <div class="p-6 divide-y divide-gray-100">
                            <div class="py-3 flex justify-between items-center first:pt-0 last:pb-0">
                                <span class="text-sm text-gray-500 font-medium">Patient</span>
                                <span class="text-sm font-bold text-gray-800">{{ $scan->patient->full_name ?? $scan->patient->name ?? '—' }}</span>
                            </div>
                            <div class="py-3 flex justify-between items-center first:pt-0 last:pb-0">
                                <span class="text-sm text-gray-500 font-medium">Doctor</span>
                                <span class="text-sm font-bold text-gray-800">Dr. {{ $scan->doctor->user->name ?? '—' }}</span>
                            </div>
                            <div class="py-3 flex justify-between items-center first:pt-0 last:pb-0">
                                <span class="text-sm text-gray-500 font-medium">Service</span>
                                <span class="text-sm font-bold text-gray-800">{{ $scan->service->name ?? '—' }}</span>
                            </div>
                            <div class="py-3 flex justify-between items-center first:pt-0 last:pb-0">
                                <span class="text-sm text-gray-500 font-medium">Scan Date</span>
                                <span class="text-sm font-bold text-gray-800">{{ $scan->created_at->format('F j, Y, g:i a') }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Image Card --}}
                    @if($scan->image_path)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                            <h2 class="text-base font-bold text-gray-800 flex items-center gap-2">
                                <x-icon name="photograph" class="w-5 h-5 text-theme-from" />
                                X-Ray Image
                            </h2>
                        </div>
                        <div class="bg-gray-900 p-4 flex items-center justify-center min-h-[300px]">
                            <img src="{{ Storage::disk(config('filesystems.default', 's3'))->url($scan->image_path) }}" alt="Dental X-Ray" class="max-h-[400px] w-auto object-contain rounded-lg shadow-2xl">
                        </div>
                    </div>
                    @endif

                    {{-- Diagnosis Card --}}
                    @if($scan->diagnosis)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                            <h2 class="text-base font-bold text-gray-800 flex items-center gap-2">
                                <x-icon name="clipboard-list" class="w-5 h-5 text-theme-from" />
                                Doctor's Diagnosis
                            </h2>
                        </div>
                        <div class="p-6">
                            <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $scan->diagnosis }}</p>
                        </div>
                    </div>
                    @endif

                </div>

                {{-- Right Column: Analysis --}}
                <div class="lg:col-span-7">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 h-full flex flex-col">
                        <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-theme-from to-theme-to">
                            <h2 class="text-lg font-bold text-white flex items-center gap-2">
                                <x-icon name="sparkles" class="w-5 h-5 text-sky-100" />
                                AI Analysis Results
                            </h2>
                        </div>
                        <div class="p-8 flex-1 prose prose-sm sm:prose-base max-w-none prose-headings:text-gray-800 prose-headings:font-bold prose-p:text-gray-600 prose-li:text-gray-600 prose-strong:text-theme-from prose-strong:font-bold">
                            @if($scan->ai_analysis)
                                {!! Str::markdown($scan->ai_analysis) !!}
                                
                                {{-- Disclaimer Alert --}}
                                <div class="mt-8 p-5 bg-orange-50 border border-orange-200 rounded-xl flex items-start gap-3 not-prose">
                                    <x-icon name="exclamation" class="w-6 h-6 text-orange-500 shrink-0 mt-0.5" />
                                    <div>
                                        <h4 class="text-sm font-bold text-orange-800 mb-1">Important Disclaimer</h4>
                                        <p class="text-sm text-orange-700 leading-relaxed">
                                            This report is an AI-generated interpretation of a radiographic image. It may be inaccurate or incomplete and is not a substitute for a comprehensive clinical examination, diagnosis, or treatment plan by a licensed dentist or dental radiologist. Always consult a qualified dental professional for personal medical advice and final diagnosis.
                                        </p>
                                    </div>
                                </div>
                            @else
                                <div class="flex flex-col items-center justify-center py-12 text-center">
                                    <x-icon name="sparkles" class="w-12 h-12 text-gray-300 mb-4" />
                                    <h3 class="text-lg font-medium text-gray-900">No AI Analysis Available</h3>
                                    <p class="mt-1 text-sm text-gray-500">This scan was created without AI analysis or the analysis failed.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    
    <style>
        @media print {
            body * {
                visibility: hidden;
            }
            .max-w-full, .max-w-full * {
                visibility: visible;
            }
            .max-w-full {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
            .bg-gradient-to-r {
                background: #fff !important;
                color: #000 !important;
                border-bottom: 2px solid #ccc;
            }
            .bg-gradient-to-r h2, .bg-gradient-to-r x-icon {
                color: #000 !important;
            }
            .shadow-sm, .shadow-2xl {
                box-shadow: none !important;
            }
        }
    </style>
</x-app-layout>
