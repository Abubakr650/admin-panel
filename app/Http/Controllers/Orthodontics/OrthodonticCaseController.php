<?php

namespace App\Http\Controllers\Orthodontics;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OrthodonticCaseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = \App\Models\Orthodontics\OrthodonticCase::with(['patient', 'doctor.user'])->withCount('sessions');

        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->whereHas('patient', function ($q) use ($searchTerm) {
                $q->where('full_name', 'LIKE', '%' . addcslashes($searchTerm, '%_\\') . '%');
            });
        }

        if ($request->input('archived') == 'true') {
            $query->onlyTrashed()->orderBy('deleted_at', 'desc');
        } else {
            $query->latest();
        }

        $cases = $query->paginate(10)->onEachSide(1)->withQueryString();

        $archivedCount = \App\Models\Orthodontics\OrthodonticCase::onlyTrashed()->count();
        $activeCount = \App\Models\Orthodontics\OrthodonticCase::where('status', 'active')->count();
        $completedCount = \App\Models\Orthodontics\OrthodonticCase::where('status', 'completed')->count();

        return view('orthodontics.cases.index', compact('cases', 'archivedCount', 'activeCount', 'completedCount'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $patients = \App\Models\Clinic\Patient::all();
        $doctors = \App\Models\Clinic\Doctor::with('user')->get();
        return view('orthodontics.cases.create', compact('patients', 'doctors'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'diagnosis' => 'nullable|string',
            'plan' => 'nullable|string',
            'total_amount' => 'required|numeric|min:0',
            'installment_amount' => 'required|numeric|min:0',
            'status' => 'required|string|in:active,completed,on_hold',
        ]);

        \App\Models\Orthodontics\OrthodonticCase::create($validated);

        return redirect()->route('orthodontics.cases.index')->with('success', 'Orthodontic case created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $case = \App\Models\Orthodontics\OrthodonticCase::with(['patient', 'doctor.user', 'sessions'])->findOrFail($id);
        return view('orthodontics.cases.show', compact('case'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $case = \App\Models\Orthodontics\OrthodonticCase::findOrFail($id);
        $patients = \App\Models\Clinic\Patient::all();
        $doctors = \App\Models\Clinic\Doctor::with('user')->get();
        return view('orthodontics.cases.edit', compact('case', 'patients', 'doctors'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $case = \App\Models\Orthodontics\OrthodonticCase::findOrFail($id);

        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'diagnosis' => 'nullable|string',
            'plan' => 'nullable|string',
            'total_amount' => 'required|numeric|min:0',
            'installment_amount' => 'required|numeric|min:0',
            'status' => 'required|string|in:active,completed,on_hold',
        ]);

        $case->update($validated);

        return redirect()->route('orthodontics.cases.index')->with('success', 'Orthodontic case updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $case = \App\Models\Orthodontics\OrthodonticCase::findOrFail($id);
        $case->delete();

        return redirect()->route('orthodontics.cases.index')->with('success', 'Orthodontic case deleted successfully.');
    }

    /**
     * Restore the specified resource from storage.
     */
    public function restore(string $id)
    {
        $case = \App\Models\Orthodontics\OrthodonticCase::withTrashed()->findOrFail($id);
        $case->restore();

        return redirect()->route('orthodontics.cases.index')->with('success', 'Orthodontic case restored successfully.');
    }
}
