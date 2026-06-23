<?php

namespace App\Http\Controllers\Radiology;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use HosseinHezami\LaravelGemini\Facades\Gemini;
use Illuminate\Support\Facades\Storage;
use App\Models\Clinic\Patient;

class RadiologyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $scans = \App\Models\Radiology\Radiology::with(['patient', 'doctor.user', 'service'])->latest()->paginate(10);
        return view('radiology.scans.index', compact('scans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $patients = \App\Models\Clinic\Patient::all();
        $doctors = \App\Models\Clinic\Doctor::with('user')->get();
        $services = \App\Models\Billing\Service::all();
        return view('radiology.scans.create', compact('patients', 'doctors', 'services'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'service_id' => 'required|exists:services,id',
            'radiology_type' => 'required|string|max:255',
            'diagnosis' => 'nullable|string',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:10240',
        ]);

        $imagePath = $request->file('image')->store('xrays', 'public');
        $fullImagePath = storage_path('app/public/' . $imagePath);

        try {
            $patient = Patient::findOrFail($request->patient_id);
            $age = $patient->age ?? 'Unknown';

            $prompt = "You are an experienced, board-certified dental and maxillofacial radiologist. Carefully and conservatively analyze a single panoramic dental X-ray image (OPG). Base your analysis ONLY on what is clearly and unambiguously visible; do NOT invent findings, fill gaps, or over-interpret subtle, noisy, or distorted areas.
                GENERAL PRINCIPLES:
                - Be cautious and conservative at all times. If you are not reasonably certain about a finding, explicitly state the uncertainty.
                - If image quality limits your assessment, clearly describe these limitations.
                - Prefer regional descriptions when individual tooth numbering is uncertain.
                            PATIENT AGE:
                - The patient's chronological age provided by the system is: $age years. You may use this age as clinical context.
                            OUTPUT STYLE:
                - Do NOT include Patient/Date/Type headers or any administrative fields.
                - Start directly with the radiographic description.
                In your output, use clear sections with headings in this order:
                    1. Image quality and limitations
                    2. Dentition and restorations
                    3. Periodontal and periapical findings
                    4. Jaws and other anatomical structures (sinuses, canals, TMJs)
                    5. Summary and suggested further imaging/clinical correlation";

            $result = Gemini::text()
                ->model('gemini-3.5-flash') 
                ->prompt($prompt)
                ->upload('image', $fullImagePath)
                ->temperature(0)
                ->maxTokens(8192)
                ->generate();

            $analysis = $result->content();

            $scan = \App\Models\Radiology\Radiology::create([
                'patient_id' => $request->patient_id,
                'doctor_id' => $request->doctor_id,
                'service_id' => $request->service_id,
                'radiology_type' => $request->radiology_type,
                'diagnosis' => $request->diagnosis,
                'image_path' => $imagePath,
                'ai_analysis' => $analysis,
            ]);

            return redirect()->route('radiology.scans.show', $scan->id)->with('success', 'Scan created and analyzed successfully.');

        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => 'AI Analysis failed: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $scan = \App\Models\Radiology\Radiology::with(['patient', 'doctor.user', 'service'])->findOrFail($id);
        return view('radiology.scans.show', compact('scan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $scan = \App\Models\Radiology\Radiology::findOrFail($id);
        $patients = \App\Models\Clinic\Patient::all();
        $doctors = \App\Models\Clinic\Doctor::with('user')->get();
        $services = \App\Models\Billing\Service::all();
        return view('radiology.scans.edit', compact('scan', 'patients', 'doctors', 'services'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
