<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs text-gray-400 uppercase tracking-wider font-medium">Radiology › Scans</p>
            <h1 class="text-xl font-bold text-gray-800">Edit Radiology Scan</h1>
        </div>
    </x-slot>

    <div class="p-6 max-w-4xl">
        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
            <form action="{{ route('radiology.scans.update', $scan) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Patient <span class="text-red-500">*</span></label>
                        <select name="patient_id" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                            @foreach($patients as $patient)
                                <option value="{{ $patient->id }}" {{ old('patient_id', $scan->patient_id) == $patient->id ? 'selected' : '' }}>
                                    {{ $patient->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Doctor <span class="text-red-500">*</span></label>
                        <select name="doctor_id" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                            @foreach($doctors as $doctor)
                                <option value="{{ $doctor->id }}" {{ old('doctor_id', $scan->doctor_id) == $doctor->id ? 'selected' : '' }}>
                                    Dr. {{ $doctor->user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Radiology Type <span class="text-red-500">*</span></label>
                        <input type="text" name="radiology_type" value="{{ old('radiology_type', $scan->radiology_type) }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Service (Billing)</label>
                        <select name="service_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                            <option value="">— Select Service —</option>
                            @foreach($services as $service)
                                <option value="{{ $service->id }}" {{ old('service_id', $scan->service_id) == $service->id ? 'selected' : '' }}>
                                    {{ $service->name }} ({{ number_format($service->price, 2) }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Diagnosis / Notes</label>
                    <textarea name="diagnosis" rows="4"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">{{ old('diagnosis', $scan->diagnosis) }}</textarea>
                </div>

                <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
                    <button type="submit"
                            class="px-5 py-2 bg-theme-from text-white text-sm font-medium rounded-lg hover:opacity-90 transition">
                        Update Scan Request
                    </button>
                    <a href="{{ route('radiology.scans.show', $scan) }}"
                       class="px-5 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
