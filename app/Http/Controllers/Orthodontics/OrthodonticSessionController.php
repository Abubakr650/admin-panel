<?php

namespace App\Http\Controllers\Orthodontics;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OrthodonticSessionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = \App\Models\Orthodontics\OrthodonticSession::with('orthodonticCase.patient');

        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->whereHas('orthodonticCase.patient', function ($q) use ($searchTerm) {
                $q->where('full_name', 'LIKE', '%' . addcslashes($searchTerm, '%_\\') . '%');
            });
        }

        if ($request->input('archived') == 'true') {
            $query->onlyTrashed()->orderBy('deleted_at', 'desc');
        } else {
            $query->latest();
        }

        $sessions = $query->paginate(10)->onEachSide(1)->withQueryString();
        $archivedCount = \App\Models\Orthodontics\OrthodonticSession::onlyTrashed()->count();

        return view('orthodontics.sessions.index', compact('sessions', 'archivedCount'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $cases = \App\Models\Orthodontics\OrthodonticCase::with('patient')->get();
        return view('orthodontics.sessions.create', compact('cases'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'case_id' => 'required|exists:orthodontic_cases,id',
            'session_date' => 'required|date',
            'treatment' => 'required|string',
            'teeth_changes' => 'nullable|string',
            'gum_changes' => 'nullable|string',
            'wire_type_upper' => 'nullable|string',
            'wire_type_lower' => 'nullable|string',
        ]);

        \App\Models\Orthodontics\OrthodonticSession::create($validated);

        return redirect()->route('orthodontics.sessions.index')->with('success', 'Orthodontic session created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $session = \App\Models\Orthodontics\OrthodonticSession::with('orthodonticCase.patient')->findOrFail($id);
        return view('orthodontics.sessions.show', compact('session'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $session = \App\Models\Orthodontics\OrthodonticSession::findOrFail($id);
        $cases = \App\Models\Orthodontics\OrthodonticCase::with('patient')->get();
        return view('orthodontics.sessions.edit', compact('session', 'cases'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $session = \App\Models\Orthodontics\OrthodonticSession::findOrFail($id);

        $validated = $request->validate([
            'case_id' => 'required|exists:orthodontic_cases,id',
            'session_date' => 'required|date',
            'treatment' => 'required|string',
            'teeth_changes' => 'nullable|string',
            'gum_changes' => 'nullable|string',
            'wire_type_upper' => 'nullable|string',
            'wire_type_lower' => 'nullable|string',
        ]);

        $session->update($validated);

        return redirect()->route('orthodontics.sessions.index')->with('success', 'Orthodontic session updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $session = \App\Models\Orthodontics\OrthodonticSession::findOrFail($id);
        $session->delete();

        return redirect()->route('orthodontics.sessions.index')->with('success', 'Orthodontic session deleted successfully.');
    }

    /**
     * Restore the specified resource from storage.
     */
    public function restore(string $id)
    {
        $session = \App\Models\Orthodontics\OrthodonticSession::withTrashed()->findOrFail($id);
        $session->restore();

        return redirect()->route('orthodontics.sessions.index')->with('success', 'Orthodontic session restored successfully.');
    }
}
