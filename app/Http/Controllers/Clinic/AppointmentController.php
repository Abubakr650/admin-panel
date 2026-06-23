<?php

namespace App\Http\Controllers\Clinic;

use App\Http\Controllers\Controller;
use App\Http\Requests\Appointment\StoreAppointmentRequest;
use App\Http\Requests\Appointment\UpdateAppointmentRequest;
use App\Models\Clinic\Appointment;
use App\Models\Clinic\Doctor;
use App\Models\Clinic\Patient;
use App\Helpers\Idempotency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $isArchived = $request->input('archived') === 'true';

        $query = Appointment::with(['patient', 'doctor.user']);

        if ($isArchived) {
            // ── Archived mode: only search is allowed, no date/status/doctor filters ──
            if ($request->filled('search')) {
                $searchTerm = $request->input('search');
                $query->where(function ($q) use ($searchTerm) {
                    $q->whereHas('patient', function ($pq) use ($searchTerm) {
                        $pq->where('full_name', 'LIKE', '%' . addcslashes($searchTerm, '%_\\') . '%');
                    })
                    ->orWhereHas('doctor.user', function ($uq) use ($searchTerm) {
                        $uq->where('name', 'LIKE', '%' . addcslashes($searchTerm, '%_\\') . '%');
                    });
                });
            }

            $query->onlyTrashed()->orderBy('deleted_at', 'desc');
        } else {
            // ── Normal mode: apply defaults then all filters ──

            // Set default values for date range and status if not provided
            if (!$request->filled('from')) {
                $request->merge(['from' => now()->format('Y-m-d')]);
            }
            if (!$request->filled('to')) {
                $request->merge(['to' => now()->format('Y-m-d')]);
            }
            if (!$request->has('status')) {
                $request->merge(['status' => 'scheduled']);
            }

            // Search
            if ($request->filled('search')) {
                $searchTerm = $request->input('search');
                $query->where(function ($q) use ($searchTerm) {
                    $q->whereHas('patient', function ($pq) use ($searchTerm) {
                        $pq->where('full_name', 'LIKE', '%' . addcslashes($searchTerm, '%_\\') . '%');
                    })
                    ->orWhereHas('doctor.user', function ($uq) use ($searchTerm) {
                        $uq->where('name', 'LIKE', '%' . addcslashes($searchTerm, '%_\\') . '%');
                    });
                });
            }

            // Status filter
            if ($request->filled('status') && $request->input('status') !== 'all') {
                $query->where('appointment_status', $request->input('status'));
            }

            // Doctor filter
            if ($request->filled('doctor_id')) {
                $query->where('doctor_id', $request->input('doctor_id'));
            }

            // Date range filter
            if ($request->filled('from')) {
                $query->whereDate('appointment_date', '>=', $request->input('from'));
            }
            if ($request->filled('to')) {
                $query->whereDate('appointment_date', '<=', $request->input('to'));
            }

            $query->latest();
        }

        $appointments = $query->paginate(10)->onEachSide(1)->withQueryString();

        // Stats (always reflect the full dataset, unaffected by current filters)
        $archivedCount  = Appointment::onlyTrashed()->count();
        $scheduledCount = Appointment::where('appointment_status', 'scheduled')->count();
        $completedCount = Appointment::where('appointment_status', 'completed')->count();
        $cancelledCount = Appointment::where('appointment_status', 'cancelled')->count();

        // Doctors list for filter dropdown
        $allDoctors = Doctor::with('user')->get()->sortBy(fn($d) => $d->user->name ?? '');

        // Fetch default consultation price for the modal
        $consultationPrice = \App\Models\Billing\Service::where('code', 'APT-001')->value('default_price') ?? 50.00;

        return view('clinic.appointments.index', compact(
            'appointments',
            'archivedCount',
            'scheduledCount',
            'completedCount',
            'cancelledCount',
            'allDoctors',
            'consultationPrice'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $patients = Patient::orderBy('full_name')->get();
        $doctors  = Doctor::where('is_active', true)
            ->with('user')->get()->sortBy(fn($d) => $d->user->name ?? '');

        // Pre-load previous appointments if a patient is pre-selected (e.g. from show page)
        $patientAppointments = collect();
        if ($request->filled('patient_id')) {
            $patientAppointments = Appointment::where('patient_id', $request->input('patient_id'))
                ->with('doctor.user')
                ->orderByDesc('appointment_date')
                ->limit(10)
                ->get();
        }

        // Pre-selected parent appointment (from "Schedule Follow-up" button)
        $parentAppointmentId = $request->input('parent_appointment_id');

        return view('clinic.appointments.create', compact(
            'patients', 'doctors', 'patientAppointments', 'parentAppointmentId'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAppointmentRequest $request)
    {
        // Idempotency check
        $key = $request->input('idempotency_key');
        if (Idempotency::check($key)) {
            return redirect()->route('appointments.index');
        }

        $validated = $request->validated();
        Appointment::create($validated);

        return redirect()->route('appointments.index')->with('success', 'Appointment created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $appointment = Appointment::with(['patient', 'doctor.user', 'returns', 'parent', 'treatments.service'])->findOrFail($id);
        return view('clinic.appointments.show', compact('appointment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $appointment = Appointment::with(['parent.doctor.user'])->findOrFail($id);
        $patients    = Patient::orderBy('full_name')->get();
        $doctors     = Doctor::where(function ($q) use ($appointment) {
            $q->where('is_active', true)->orWhere('id', $appointment->doctor_id);
        })->with('user')->get()->sortBy(fn($d) => $d->user->name ?? '');

        // Previous appointments of this patient (excluding itself)
        $patientAppointments = Appointment::where('patient_id', $appointment->patient_id)
            ->where('id', '!=', $appointment->id)
            ->with('doctor.user')
            ->orderByDesc('appointment_date')
            ->limit(10)
            ->get();

        return view('clinic.appointments.edit', compact(
            'appointment', 'patients', 'doctors', 'patientAppointments'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAppointmentRequest $request, string $id)
    {
        // Idempotency check
        $key = $request->input('idempotency_key');
        if (Idempotency::check($key)) {
            return redirect()->route('appointments.index');
        }

        $validated = $request->validated();
        $appointment = Appointment::findOrFail($id);
        $appointment->update($validated);

        return redirect()->route('appointments.index')->with('success', 'Appointment updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $appointment = Appointment::withTrashed()->findOrFail($id);
        $appointment->delete();
        $previousUrl = URL::previous();
        $showRoute   = route('appointments.show', $appointment->id);
        if (str_contains($previousUrl, $showRoute)) {
            return redirect()->route('appointments.index')->with('success', 'Appointment archived successfully!');
        }
        return redirect()->back()->with('success', 'Appointment archived successfully!');
    }

    /**
     * Restore the specified resource.
     */
    public function restore(string $id)
    {
        $appointment = Appointment::withTrashed()
            ->with([
                'doctor' => fn($q) => $q->withTrashed(),
                'patient' => fn($q) => $q->withTrashed(),
            ])
            ->findOrFail($id);

        $doctorTrashed  = $appointment->doctor  && $appointment->doctor->trashed();
        $patientTrashed = $appointment->patient && $appointment->patient->trashed();

        if ($doctorTrashed && $patientTrashed) {
            return redirect()->route('appointments.index', ['archived' => 'true'])
                ->with('error', 'Cannot restore this appointment because both the assigned doctor and patient are archived. Please restore them first.');
        }

        if ($doctorTrashed) {
            return redirect()->route('appointments.index', ['archived' => 'true'])
                ->with('error', 'Cannot restore this appointment because the assigned doctor is archived. Please restore the doctor first.');
        }

        if ($patientTrashed) {
            return redirect()->route('appointments.index', ['archived' => 'true'])
                ->with('error', 'Cannot restore this appointment because the assigned patient is archived. Please restore the patient first.');
        }

        $appointment->restore();

        return redirect()->route('appointments.index', ['archived' => 'true'])->with('success', 'Appointment restored successfully!');
    }

    /**
     * Mark the specified appointment as completed.
     */
    public function complete(Request $request, string $id)
    {
        $appointment = Appointment::findOrFail($id);
        $appointment->update(['appointment_status' => 'completed']);

        // 1. Ensure the "Appointment" service exists (find by code to avoid duplicate key errors)
        $service = \App\Models\Billing\Service::firstOrCreate(
            ['code' => 'APT-001'],
            [
                'name' => 'Consultation',
                'default_price' => 50.00,
                'duration_minutes' => 30,
                'is_active' => true,
            ]
        );

        // 2. Automatically create a Treatment record for this appointment
        $treatment = \App\Models\Clinic\Treatment::firstOrCreate(
            [
                'appointment_id' => $appointment->id,
                'service_id'     => $service->id,
            ],
            [
                'patient_id'     => $appointment->patient_id,
                'doctor_id'      => $appointment->doctor_id,
                'type'           => \App\Models\Clinic\Treatment::TYPE_CONSULTATION,
                'quantity'       => 1,
                'price'          => $service->default_price,
                'total'          => $service->default_price,
                'status'         => \App\Models\Clinic\Treatment::STATUS_COMPLETED,
                'billing_status' => \App\Models\Clinic\Treatment::BILLING_PENDING,
                'created_by'     => auth()->id(),
            ]
        );

        // 3. Automatically generate the invoice if not already billed
        $invoiceCreated = false;
        if ($treatment->wasRecentlyCreated || $treatment->billing_status !== \App\Models\Clinic\Treatment::BILLING_BILLED) {
            
            // Get or create a default currency
            $currency = \App\Models\Billing\Currency::firstOrCreate(
                ['code' => 'USD'],
                ['name' => 'US Dollar']
            );

            // Generate invoice number
            $invoiceCount = \App\Models\Billing\Invoice::withTrashed()->count() + 1;
            $invoiceNumber = 'INV-' . str_pad($invoiceCount, 5, '0', STR_PAD_LEFT);

            $invoice = \App\Models\Billing\Invoice::create([
                'invoice_number'   => $invoiceNumber,
                'patient_id'       => $appointment->patient_id,
                'doctor_id'        => $appointment->doctor_id,
                'total_amount'     => $treatment->total,
                'discount_percent' => 0,
                'final_amount'     => $treatment->total,
                'payment_status'   => 'unpaid',
                'exchange_rate'    => 1.0,
                'currency_id'      => $currency->id,
                'created_by'       => auth()->id(),
            ]);

            // Create invoice item
            \App\Models\Billing\InvoiceItem::create([
                'invoice_id'   => $invoice->id,
                'treatment_id' => $treatment->id,
                'quantity'     => $treatment->quantity,
                'unit_price'   => $treatment->price,
                'total_price'  => $treatment->total,
            ]);

            // Update treatment status to billed
            $treatment->update(['billing_status' => \App\Models\Clinic\Treatment::BILLING_BILLED]);
            $invoiceCreated = true;

            // 4. Process Payment if provided
            if ($request->filled('amount_paid') && $request->amount_paid > 0) {
                $amountPaid = (float) $request->amount_paid;
                
                \App\Models\Billing\Payment::create([
                    'invoice_id'     => $invoice->id,
                    'amount'         => $amountPaid,
                    'payment_method' => $request->input('payment_method', 'cash'),
                    'paid_at'        => now(),
                    'exchange_rate'  => 1.0,
                    'currency_id'    => $currency->id,
                    'created_by'     => auth()->id(),
                ]);

                // Update invoice payment status
                if ($amountPaid >= $invoice->final_amount) {
                    $invoice->update(['payment_status' => 'paid']);
                } else {
                    $invoice->update(['payment_status' => 'partial']);
                }
            }
        }

        // 5. Redirect back without going to another page
        return redirect()->back()
            ->with('success', 'Appointment completed, invoice generated, and payment processed successfully.');
    }
    /**
     * Return the last appointments for a given patient (AJAX).
     */
    public function getPatientAppointments(Request $request, string $patientId)
    {
        $appointments = Appointment::where('patient_id', $patientId)
            ->with('doctor.user')
            ->orderByDesc('appointment_date')
            ->limit(10)
            ->get()
            ->map(fn($a) => [
                'id'     => $a->id,
                'label'  => $a->appointment_date?->format('M d, Y') .
                            ' — Dr. ' . ($a->doctor->user->name ?? '?') .
                            ' (' . ucfirst(str_replace('_', ' ', $a->appointment_status)) . ')',
                'date'   => $a->appointment_date?->format('M d, Y'),
                'doctor' => $a->doctor->user->name ?? '—',
                'status' => ucfirst(str_replace('_', ' ', $a->appointment_status)),
            ]);

        return response()->json($appointments);
    }
}
