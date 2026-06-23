<?php

namespace App\Http\Controllers\Clinic;

use App\Http\Controllers\Controller;
use App\Http\Requests\Patient\StorePatientRequest;
use App\Http\Requests\Patient\UpdatePatientRequest;
use Illuminate\Http\Request;
use App\Models\Clinic\Patient;
use App\Helpers\Idempotency;
use Illuminate\Support\Facades\URL;

class PatientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Patient::query();

        // Add search functionality
        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('full_name', 'LIKE', '%' . addcslashes($searchTerm, '%_\\') . '%')
                  ->orWhere('phone',     'LIKE', '%' . addcslashes($searchTerm, '%_\\') . '%');
            });
        }

        // Gender filter (active patients only)
        if ($request->filled('gender') && in_array($request->input('gender'), ['male', 'female'])) {
            $query->where('gender', $request->input('gender'));
        }

        // Archived
        if ($request->input('archived') == 'true') {
            $query->onlyTrashed()->orderBy('deleted_at', 'desc');
        } else {
            $query->latest();
        }

        $patients = $query->paginate(10)->onEachSide(1)->withQueryString();
        $archivedCount = Patient::onlyTrashed()->count();


        $maleCount   = Patient::where('gender', 'male')->count();
        $femaleCount = Patient::where('gender', 'female')->count();

        return view('clinic.patients.index', compact('patients', 'archivedCount', 'maleCount', 'femaleCount'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $returnTo = $request->input('return_to');
        return view('clinic.patients.create', compact('returnTo'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePatientRequest $request)
    {
        // Idempotency check
        $key = $request->input('idempotency_key');
        if (Idempotency::check($key)) {
            return redirect($request->input('return_to', route('patients.index')));
        }

        $validated = $request->validated();
        $patient = Patient::create($validated);

        if ($request->filled('return_to')) {
            $returnTo = $request->input('return_to');
            $params = $request->except(['_token', 'idempotency_key', 'return_to']);
            $params['patient_id'] = $patient->id;
            
            $separator = (strpos($returnTo, '?') === false) ? '?' : '&';
            return redirect($returnTo . $separator . http_build_query($params))
                ->with('success', 'Patient created successfully!');
        }

        return redirect()->route('patients.index')->with('success', 'Patient created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        $patient = Patient::findOrFail($id);
        $activeTab = strtolower(trim($request->input('tab', 'appointments'))) ?: 'appointments';

        // Initial counts for all tabs
        $counts = [
            'appointments' => $patient->appointments()->count(),
            'invoices'     => $patient->invoices()->count(),
            'radiology'    => $patient->radiologies()->count(),
            'orthodontics' => $patient->orthodonticCases()->count(),
        ];

        // Fetch data ONLY for the active tab
        $data = $this->getTabData($patient, $activeTab, $request);

        return view('clinic.patients.show', array_merge([
            'patient'   => $patient,
            'counts'    => $counts,
            'activeTab' => $activeTab,
        ], $data));
    }

    /**
     * Get data for the specific tab.
     */
    private function getTabData(Patient $patient, string $tab, Request $request): array
    {
        $tabs = ['appointments', 'invoices', 'radiology', 'orthodontics'];
        $result = [];

        foreach ($tabs as $t) {
            if ($t === $tab) {
                $method = 'get' . ucfirst($t) . 'Data';
                $result[$t === 'orthodontics' ? 'orthodonticCases' : ($t === 'radiology' ? 'radiologies' : $t)] = $this->$method($patient, $request);
            } else {
                // Return empty paginator for non-active tabs to avoid view errors
                $key = $t === 'orthodontics' ? 'orthodonticCases' : ($t === 'radiology' ? 'radiologies' : $t);
                $result[$key] = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 5);
            }
        }

        return $result;
    }

    private function getAppointmentsData(Patient $patient, Request $request)
    {
        return $patient->appointments()->with('doctor.user')->latest()
            ->when($request->input('search'), function($q, $search) {
                $q->whereHas('doctor.user', function($dq) use ($search) {
                    $dq->where('name', 'LIKE', '%' . addcslashes($search, '%_\\') . '%');
                });
            })
            ->when($request->input('status'), fn($q, $status) => $q->where('appointment_status', $status))
            ->paginate(5, ['*'], 'appointments_page')->withQueryString();
    }

    private function getInvoicesData(Patient $patient, Request $request)
    {
        return $patient->invoices()->latest()
            ->when($request->input('search'), fn($q, $search) => $q->where('invoice_number', 'LIKE', '%' . addcslashes($search, '%_\\') . '%'))
            ->when($request->input('status'), fn($q, $status) => $q->where('payment_status', $status))
            ->paginate(5, ['*'], 'invoices_page')->withQueryString();
    }

    private function getRadiologyData(Patient $patient, Request $request)
    {
        return $patient->radiologies()->latest()
            ->when($request->input('search'), fn($q, $search) => $q->where('radiology_type', 'LIKE', '%' . addcslashes($search, '%_\\') . '%'))
            ->paginate(5, ['*'], 'radiology_page')->withQueryString();
    }

    private function getOrthodonticsData(Patient $patient, Request $request)
    {
        return $patient->orthodonticCases()->latest()
            ->when($request->input('search'), fn($q, $search) => $q->where('diagnosis', 'LIKE', '%' . addcslashes($search, '%_\\') . '%'))
            ->when($request->input('status'), fn($q, $status) => $q->where('status', $status))
            ->paginate(5, ['*'], 'ortho_page')->withQueryString();
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $patient = Patient::findOrFail($id);
        return view('clinic.patients.edit', compact('patient'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePatientRequest $request, string $id)
    {
         // Idempotency check
        $key = $request->input('idempotency_key');
        if (Idempotency::check($key)) {
            return redirect()->route('patients.index')->with('success', 'Patient updated successfully!');
        }

        $validated = $request->validated();
        $patient = Patient::findOrFail($id);   
        $patient->update($validated);
        return redirect()->route('patients.index')->with('success', 'Patient updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $patient = Patient::withTrashed()->findOrFail($id);

        if ($patient->hasProtectedRelations()) {
            return back()->with('error', 'Cannot archive patient because it has associated records.');
        }

        $patient->delete();
        $previousUrl = URL::previous();
        $showRoute   = route('patients.show', $patient->id);
        if (str_contains($previousUrl, $showRoute)) {
            return redirect()->route('patients.index')->with('success', 'Patient archived successfully!');
        }
        return redirect()->back()->with('success', 'Patient archived successfully!');
    }

    public function restore(string $id)
    {
        $patient = Patient::withTrashed()->findOrFail($id);
        $patient->restore();
        return redirect()->route('patients.index', ['archived' => 'true'])->with('success', 'Patient restored successfully!');
    }
}
