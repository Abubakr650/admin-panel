<?php

namespace App\Http\Controllers\Clinic;

use App\Http\Controllers\Controller;
use App\Models\Clinic\Treatment;
use App\Models\Clinic\Patient;
use App\Models\Clinic\Doctor;
use App\Models\Billing\Service;
use App\Models\Pharmacy\PharmacyBatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class TreatmentController extends Controller
{
    /**
     * Display a listing of treatments.
     */
    public function index(Request $request)
    {
        $isArchived = $request->input('archived') === 'true';

        $query = Treatment::with(['patient', 'doctor.user', 'service', 'pharmacyBatch.pharmacyItem']);

        if ($isArchived) {
            if ($request->filled('search')) {
                $term = $request->input('search');
                $query->where(function ($q) use ($term) {
                    $q->whereHas('patient', fn($pq) => $pq->where('full_name', 'LIKE', '%' . addcslashes($term, '%_\\') . '%'))
                      ->orWhereHas('doctor.user', fn($uq) => $uq->where('name', 'LIKE', '%' . addcslashes($term, '%_\\') . '%'));
                });
            }
            $query->onlyTrashed()->orderBy('deleted_at', 'desc');
        } else {
            // Search
            if ($request->filled('search')) {
                $term = $request->input('search');
                $query->where(function ($q) use ($term) {
                    $q->whereHas('patient', fn($pq) => $pq->where('full_name', 'LIKE', '%' . addcslashes($term, '%_\\') . '%'))
                      ->orWhereHas('doctor.user', fn($uq) => $uq->where('name', 'LIKE', '%' . addcslashes($term, '%_\\') . '%'))
                      ->orWhere('description', 'LIKE', '%' . addcslashes($term, '%_\\') . '%');
                });
            }

            // Type filter
            if ($request->filled('type') && $request->input('type') !== 'all') {
                $query->where('type', $request->input('type'));
            }

            // Status filter
            if ($request->filled('status') && $request->input('status') !== 'all') {
                $query->where('status', $request->input('status'));
            }

            // Billing status filter
            if ($request->filled('billing_status') && $request->input('billing_status') !== 'all') {
                $query->where('billing_status', $request->input('billing_status'));
            }

            $query->latest();
        }

        $treatments = $query->paginate(15)->onEachSide(1)->withQueryString();

        // Stats
        $archivedCount = Treatment::onlyTrashed()->count();
        $totalCount    = Treatment::count();
        $completedCount = Treatment::where('status', Treatment::STATUS_COMPLETED)->count();
        $pendingBillingCount = Treatment::where('billing_status', Treatment::BILLING_PENDING)->count();

        return view('clinic.treatments.index', compact(
            'treatments', 'archivedCount', 'totalCount', 'completedCount', 'pendingBillingCount'
        ));
    }

    /**
     * Show the form for creating a new treatment.
     */
    public function create(Request $request)
    {
        $patients = Patient::orderBy('full_name')->get();
        $doctors  = Doctor::where('is_active', true)->with('user')->get()->sortBy(fn($d) => $d->user->name ?? '');
        $services = Service::where('is_active', true)->orderBy('name')->get();
        $batches  = PharmacyBatch::with('pharmacyItem')
                        ->where('remaining_quantity', '>', 0)
                        ->where('expiry_date', '>=', now())
                        ->get();

        // Pre-select patient/appointment if passed
        $selectedPatientId     = $request->input('patient_id');
        $selectedAppointmentId = $request->input('appointment_id');

        return view('clinic.treatments.create', compact(
            'patients', 'doctors', 'services', 'batches',
            'selectedPatientId', 'selectedAppointmentId'
        ));
    }

    /**
     * Store a newly created treatment.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id'        => 'required|exists:patients,id',
            'doctor_id'         => 'required|exists:doctors,id',
            'appointment_id'    => 'nullable|exists:appointments,id',
            'service_id'        => 'nullable|exists:services,id',
            'pharmacy_batch_id' => 'nullable|exists:pharmacy_batches,id',
            'type'              => 'required|string',
            'quantity'          => 'required|integer|min:1',
            'price'             => 'required|numeric|min:0',
            'discount'          => 'nullable|numeric|min:0',
            'description'       => 'nullable|string',
            'status'            => 'required|string',
        ]);

        $validated['discount'] = $validated['discount'] ?? 0;
        $validated['total'] = max(0, ($validated['price'] * $validated['quantity']) - $validated['discount']);
        $validated['billing_status'] = Treatment::BILLING_PENDING;
        $validated['created_by'] = auth()->id();

        $treatment = Treatment::create($validated);

        // If pharmacy dispense, decrement batch quantity
        if ($treatment->pharmacy_batch_id) {
            $batch = PharmacyBatch::find($treatment->pharmacy_batch_id);
            if ($batch && $batch->remaining_quantity >= $treatment->quantity) {
                $batch->decrement('remaining_quantity', $treatment->quantity);
            }
        }

        return redirect()->route('treatments.index')->with('success', 'Treatment created successfully!');
    }

    /**
     * Display the specified treatment.
     */
    public function show(string $id)
    {
        $treatment = Treatment::with(['patient', 'doctor.user', 'service', 'pharmacyBatch.pharmacyItem', 'invoiceItems.invoice'])
            ->findOrFail($id);
        return view('clinic.treatments.show', compact('treatment'));
    }

    /**
     * Show the form for editing the specified treatment.
     */
    public function edit(string $id)
    {
        $treatment = Treatment::findOrFail($id);
        $patients  = Patient::orderBy('full_name')->get();
        $doctors   = Doctor::where(fn($q) => $q->where('is_active', true)->orWhere('id', $treatment->doctor_id))
                        ->with('user')->get()->sortBy(fn($d) => $d->user->name ?? '');
        $services  = Service::where('is_active', true)->orderBy('name')->get();

        return view('clinic.treatments.edit', compact('treatment', 'patients', 'doctors', 'services'));
    }

    /**
     * Update the specified treatment.
     */
    public function update(Request $request, string $id)
    {
        $treatment = Treatment::findOrFail($id);

        $rules = [
            'patient_id'  => 'required|exists:patients,id',
            'doctor_id'   => 'required|exists:doctors,id',
            'type'        => 'required|string',
            'description' => 'nullable|string',
            'status'      => 'required|string',
        ];

        // Only validate and update financial fields if the treatment is not yet billed.
        if ($treatment->billing_status === Treatment::BILLING_PENDING) {
            $rules['quantity'] = 'required|integer|min:1';
            $rules['price']    = 'required|numeric|min:0';
            $rules['discount'] = 'nullable|numeric|min:0';
        }

        $validated = $request->validate($rules);

        if ($treatment->billing_status === Treatment::BILLING_PENDING) {
            $validated['discount'] = $validated['discount'] ?? 0;
            $validated['total'] = max(0, ($validated['price'] * $validated['quantity']) - $validated['discount']);
        }

        $validated['updated_by'] = auth()->id();

        $treatment->update($validated);

        return redirect()->route('treatments.index')->with('success', 'Treatment updated successfully!');
    }

    /**
     * Remove the specified treatment.
     */
    public function destroy(string $id)
    {
        $treatment = Treatment::withTrashed()->findOrFail($id);
        $treatment->delete();

        $previousUrl = URL::previous();
        if (str_contains($previousUrl, route('treatments.show', $treatment->id))) {
            return redirect()->route('treatments.index')->with('success', 'Treatment archived successfully!');
        }
        return redirect()->back()->with('success', 'Treatment archived successfully!');
    }

    /**
     * Restore a soft-deleted treatment.
     */
    public function restore(string $id)
    {
        $treatment = Treatment::withTrashed()->findOrFail($id);
        $treatment->restore();
        return redirect()->route('treatments.index', ['archived' => 'true'])->with('success', 'Treatment restored successfully!');
    }

    /**
     * AJAX: Get service price for auto-fill.
     */
    public function getServicePrice(string $serviceId)
    {
        $service = Service::findOrFail($serviceId);
        return response()->json([
            'default_price' => $service->default_price,
            'name' => $service->name,
            'code' => $service->code,
        ]);
    }

    /**
     * AJAX: Get pharmacy batch info for auto-fill.
     */
    public function getBatchPrice(string $batchId)
    {
        $batch = PharmacyBatch::with('pharmacyItem')->findOrFail($batchId);
        return response()->json([
            'default_price'      => $batch->pharmacyItem->default_price,
            'name'               => $batch->pharmacyItem->commercial_name,
            'remaining_quantity' => $batch->remaining_quantity,
        ]);
    }
}
