<?php

namespace App\Http\Controllers\Radiology;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use App\Models\Clinic\Patient;
use App\Services\FileStorageService;

class RadiologyController extends Controller
{
    protected FileStorageService $storage;

    public function __construct(FileStorageService $storage)
    {
        $this->storage = $storage;
    }

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
            'image' => 'required|image|max:10240',
        ]);

        // Upload image to S3 using FileStorageService (same pattern as UserController)
        $imagePath = $this->storage->upload($request->file('image'), 'xrays', 'xray');

        try {
            $patient = Patient::findOrFail($request->patient_id);
            $age = $patient->age ?? 'Unknown';

            // Read image and encode to base64 for Gemini API
            $imageContent = file_get_contents($request->file('image')->getRealPath());
            $base64Image = base64_encode($imageContent);
            $mimeType = $request->file('image')->getMimeType(); // e.g. image/jpeg

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

            // Direct Gemini API call (bypassing library bug with ConnectionException)
            $apiKey = config('gemini.api_key');
            $model = 'gemini-2.5-flash';

            $response = Http::timeout(120)
                ->connectTimeout(30)
                ->retry(2, 2000)
                ->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}", [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt],
                                [
                                    'inline_data' => [
                                        'mime_type' => $mimeType,
                                        'data' => $base64Image,
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'generationConfig' => [
                        'temperature' => 0,
                        'maxOutputTokens' => 8192,
                    ],
                ]);

            if (!$response->successful()) {
                throw new \Exception('Gemini API error: ' . ($response->json('error.message') ?? $response->body()));
            }

            $analysis = $response->json('candidates.0.content.parts.0.text', '');

            if (empty($analysis)) {
                throw new \Exception('Gemini returned an empty analysis.');
            }

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
            // Clean up uploaded S3 file on failure
            $this->storage->delete($imagePath);
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
