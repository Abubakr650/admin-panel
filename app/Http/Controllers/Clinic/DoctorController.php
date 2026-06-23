<?php

namespace App\Http\Controllers\Clinic;

use App\Http\Controllers\Controller;
use App\Http\Requests\Doctor\StoreDoctorRequest;
use App\Http\Requests\Doctor\UpdateDoctorRequest;
use App\Helpers\Idempotency;
use App\Models\Clinic\Doctor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class DoctorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Doctor::with('user');

        // Search functionality (name, specialty, degree)
        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->whereHas('user', function ($uq) use ($searchTerm) {
                    $uq->where('name', 'LIKE', '%' . addcslashes($searchTerm, '%_\\') . '%');
                })
                ->orWhere('specialty', 'LIKE', '%' . addcslashes($searchTerm, '%_\\') . '%')
                ->orWhere('degree',    'LIKE', '%' . addcslashes($searchTerm, '%_\\') . '%');
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $status = $request->input('status');
            if ($status === 'active') {
                $query->where('is_active', true);
            } elseif ($status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Archived
        if ($request->input('archived') == 'true') {
            $query->onlyTrashed()->orderBy('deleted_at', 'desc');
        } else {
            $query->latest();
        }

        $doctors = $query->paginate(10)->onEachSide(1)->withQueryString();

        // Stats
        $archivedCount = Doctor::onlyTrashed()->count();
        $activeCount   = Doctor::where('is_active', true)->count();
        $inactiveCount = Doctor::where('is_active', false)->count();

        return view('clinic.doctors.index', compact('doctors', 'archivedCount', 'activeCount', 'inactiveCount'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Only users with role 'doctor' who don't have a profile yet
        $users = User::where('role', 'doctor')
                    ->whereDoesntHave('doctor')
                    ->get();
        return view('clinic.doctors.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDoctorRequest $request)
    {
        // Idempotency check
        $key = $request->input('idempotency_key');
        if (Idempotency::check($key)) {
            return redirect()->route('doctors.index');
        }

        $validated = $request->validated();
        Doctor::create($validated);
        return redirect()->route('doctors.index')->with('success', 'Doctor created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        $doctor = Doctor::with('user')->findOrFail($id);
        $activeTab = strtolower(trim($request->input('tab', 'appointments'))) ?: 'appointments';

        // Initial counts for all tabs
        $counts = [
            'appointments' => $doctor->appointments()->count(),
            'invoices'     => $doctor->invoices()->count(),
            'radiology'    => $doctor->radiologies()->count(),
            'orthodontics' => $doctor->orthodonticCases()->count(),
        ];

        // Fetch data ONLY for the active tab
        $data = $this->getTabData($doctor, $activeTab, $request);

        return view('clinic.doctors.show', array_merge([
            'doctor'    => $doctor,
            'counts'    => $counts,
            'activeTab' => $activeTab,
        ], $data));
    }

    /**
     * Get data for the specific tab.
     */
    private function getTabData(Doctor $doctor, string $tab, Request $request): array
    {
        $tabs = ['appointments', 'invoices', 'radiology', 'orthodontics'];
        $result = [];

        foreach ($tabs as $t) {
            if ($t === $tab) {
                $method = 'get' . ucfirst($t) . 'Data';
                $result[$t === 'orthodontics' ? 'orthodonticCases' : ($t === 'radiology' ? 'radiologies' : $t)] = $this->$method($doctor, $request);
            } else {
                // Return empty paginator for non-active tabs to avoid view errors
                $key = $t === 'orthodontics' ? 'orthodonticCases' : ($t === 'radiology' ? 'radiologies' : $t);
                $result[$key] = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 5);
            }
        }

        return $result;
    }

    private function getAppointmentsData(Doctor $doctor, Request $request)
    {
        return $doctor->appointments()->with('patient')->latest()
            ->when($request->input('search'), function($q, $search) {
                $q->whereHas('patient', function($pq) use ($search) {
                    $pq->where('full_name', 'LIKE', '%' . addcslashes($search, '%_\\') . '%');
                });
            })
            ->when($request->input('status'), fn($q, $status) => $q->where('appointment_status', $status))
            ->when($request->input('from'),   fn($q, $from)   => $q->whereDate('appointment_date', '>=', $from))
            ->when($request->input('to'),     fn($q, $to)     => $q->whereDate('appointment_date', '<=', $to))
            ->paginate(5, ['*'], 'appointments_page')->withQueryString();
    }

    private function getInvoicesData(Doctor $doctor, Request $request)
    {
        return $doctor->invoices()->with('patient')->latest()
            ->when($request->input('search'), function($q, $search) {
                $q->where(function($sub) use ($search) {
                    $sub->whereHas('patient', function($pq) use ($search) {
                        $pq->where('full_name', 'LIKE', '%' . addcslashes($search, '%_\\') . '%');
                    })->orWhere('invoice_number', 'LIKE', '%' . addcslashes($search, '%_\\') . '%');
                });
            })
            ->when($request->input('status'), fn($q, $status) => $q->where('payment_status', $status))
            ->paginate(5, ['*'], 'invoices_page')->withQueryString();
    }

    private function getRadiologyData(Doctor $doctor, Request $request)
    {
        return $doctor->radiologies()->with('patient')->latest()
            ->when($request->input('search'), function($q, $search) {
                $q->where(function($sub) use ($search) {
                    $sub->whereHas('patient', function($pq) use ($search) {
                        $pq->where('full_name', 'LIKE', '%' . addcslashes($search, '%_\\') . '%');
                    })->orWhere('radiology_type', 'LIKE', '%' . addcslashes($search, '%_\\') . '%');
                });
            })
            ->paginate(5, ['*'], 'radiology_page')->withQueryString();
    }

    private function getOrthodonticsData(Doctor $doctor, Request $request)
    {
        return $doctor->orthodonticCases()->with('patient')->latest()
            ->when($request->input('search'), function($q, $search) {
                $q->where(function($sub) use ($search) {
                    $sub->whereHas('patient', function($pq) use ($search) {
                        $pq->where('full_name', 'LIKE', '%' . addcslashes($search, '%_\\') . '%');
                    })->orWhere('diagnosis', 'LIKE', '%' . addcslashes($search, '%_\\') . '%');
                });
            })
            ->when($request->input('status'), fn($q, $status) => $q->where('status', $status))
            ->paginate(5, ['*'], 'ortho_page')->withQueryString();
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $doctor = Doctor::findOrFail($id);
        
        // Users with role 'doctor' who either don't have a profile OR is the current user
        $users = User::where('role', 'doctor')
                    ->where(function($q) use ($doctor) {
                        $q->whereDoesntHave('doctor')
                          ->orWhere('id', $doctor->user_id);
                    })
                    ->get();
                    
        return view('clinic.doctors.edit', compact('doctor', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDoctorRequest $request, string $id)
    {
        // Idempotency check
        $key = $request->input('idempotency_key');
        if (Idempotency::check($key)) {
            return redirect()->route('doctors.index');
        }

        $validated = $request->validated();
        $doctor = Doctor::findOrFail($id);
        $doctor->update($validated);
        return redirect()->route('doctors.index')->with('success', 'Doctor updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $doctor = Doctor::withTrashed()->findOrFail($id);

        if ($doctor->hasProtectedRelations()) {
            return back()->with('error', 'Cannot archive doctor because it has associated records.');
        }

        $doctor->delete();
        $previousUrl = URL::previous();
        $showRoute   = route('doctors.show', $doctor->id);
        if (str_contains($previousUrl, $showRoute)) {
            return redirect()->route('doctors.index')->with('success', 'Doctor archived successfully!');
        }
        return redirect()->back()->with('success', 'Doctor archived successfully!');
    }

    public function restore(string $id)
    {
        $doctor = Doctor::withTrashed()->with(['user' => fn($q) => $q->withTrashed()])->findOrFail($id);
        
        if ($doctor->user && $doctor->user->trashed()) {
            return redirect()->route('doctors.index', ['archived' => 'true'])
                ->with('error', 'The doctor cannot be restored because his user account has been archived. Please restore the user first.');
        }

        $doctor->restore();
        return redirect()->route('doctors.index', ['archived' => 'true'])->with('success', 'Doctor restored successfully!');
    }
}
