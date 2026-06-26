<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Clinic\PatientController;
use App\Http\Controllers\Clinic\DoctorController;
use App\Http\Controllers\Clinic\AppointmentController;
use App\Http\Controllers\Clinic\TreatmentController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DashboardController;

Route::get('/', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ─── Admin ────────────────────────────────────────────────────────────────
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
    Route::put('/users/{id}/restore', [\App\Http\Controllers\Admin\UserController::class, 'restore'])->name('users.restore');
    Route::post('/theme/update', [\App\Http\Controllers\Admin\ThemeController::class, 'update'])->name('theme.update');

    // ─── Clinic ───────────────────────────────────────────────────────────────

    // Patient Routes
    Route::resource('/patients', PatientController::class);
    Route::put('/patients/{id}/restore', [PatientController::class, 'restore'])->name('patients.restore');
    // Doctor Routes
    Route::resource('doctors', DoctorController::class);
    Route::put('/doctors/{id}/restore', [DoctorController::class, 'restore'])->name('doctors.restore');
    // Appointment Routes
    Route::resource('appointments', AppointmentController::class);
    Route::put('/appointments/{id}/restore',  [AppointmentController::class, 'restore'])->name('appointments.restore');
    Route::put('/appointments/{id}/complete', [AppointmentController::class, 'complete'])->name('appointments.complete');
    Route::get('/appointments/patient/{patientId}/previous', [AppointmentController::class, 'getPatientAppointments'])->name('appointments.patient.previous');
    // Treatment Routes
    Route::resource('treatments', TreatmentController::class);
    Route::put('/treatments/{id}/restore', [TreatmentController::class, 'restore'])->name('treatments.restore');
    Route::get('/api/services/{service}/price', [TreatmentController::class, 'getServicePrice'])->name('api.service.price');
    Route::get('/api/batches/{batch}/price', [TreatmentController::class, 'getBatchPrice'])->name('api.batch.price');

    // ─── Billing ──────────────────────────────────────────────────────────────
    Route::resource('invoices',   \App\Http\Controllers\Billing\InvoiceController::class);
    Route::put('/invoices/{id}/restore', [\App\Http\Controllers\Billing\InvoiceController::class, 'restore'])->name('invoices.restore');
    Route::resource('payments',   \App\Http\Controllers\Billing\PaymentController::class);
    Route::put('/payments/{id}/restore', [\App\Http\Controllers\Billing\PaymentController::class, 'restore'])->name('payments.restore');
    Route::resource('services',   \App\Http\Controllers\Billing\ServiceController::class);
    Route::put('/services/{id}/restore', [\App\Http\Controllers\Billing\ServiceController::class, 'restore'])->name('services.restore');
    Route::post('currencies/convert', [\App\Http\Controllers\Billing\CurrencyController::class, 'convert'])->name('currencies.convert');
    Route::resource('currencies', \App\Http\Controllers\Billing\CurrencyController::class);

    // ─── Pharmacy ─────────────────────────────────────────────────────────────
   
    // Items
    Route::resource('pharmacy/items',     \App\Http\Controllers\Pharmacy\PharmacyItemController::class)->names('pharmacy.items');
    Route::put('/pharmacy/items/{id}/restore', [\App\Http\Controllers\Pharmacy\PharmacyItemController::class, 'restore'])->name('pharmacy.items.restore');
    // Batches
    Route::resource('pharmacy/batches',   \App\Http\Controllers\Pharmacy\PharmacyBatchController::class)->names('pharmacy.batches');
    Route::put('/pharmacy/batches/{id}/restore', [\App\Http\Controllers\Pharmacy\PharmacyBatchController::class, 'restore'])->name('pharmacy.batches.restore');
    // Suppliers
    Route::resource('pharmacy/suppliers', \App\Http\Controllers\Pharmacy\SupplierController::class)->names('pharmacy.suppliers');
    Route::put('/pharmacy/suppliers/{id}/restore', [\App\Http\Controllers\Pharmacy\SupplierController::class, 'restore'])->name('pharmacy.suppliers.restore');
    // Dispense (Pharmacy Sales)
    Route::get('pharmacy/dispense', [\App\Http\Controllers\Pharmacy\PharmacyDispenseController::class, 'index'])->name('pharmacy.dispense');
    Route::post('pharmacy/dispense', [\App\Http\Controllers\Pharmacy\PharmacyDispenseController::class, 'store'])->name('pharmacy.dispense.store');
    Route::get('pharmacy/dispense/{invoice}/print', [\App\Http\Controllers\Pharmacy\PharmacyDispenseController::class, 'print'])->name('pharmacy.dispense.print');
    Route::get('api/patients/{patient}/invoices', [\App\Http\Controllers\Pharmacy\PharmacyDispenseController::class, 'patientInvoices'])->name('api.patient.invoices');
    // Warehouse
    Route::resource('pharmacy/warehouse', \App\Http\Controllers\Pharmacy\WarehouseItemController::class)->names('pharmacy.warehouse');
    Route::put('/pharmacy/warehouse/{id}/restore', [\App\Http\Controllers\Pharmacy\WarehouseItemController::class, 'restore'])->name('pharmacy.warehouse.restore');

    // ─── Radiology ────────────────────────────────────────────────────────────
    Route::resource('radiology/scans',  \App\Http\Controllers\Radiology\RadiologyController::class)->names('radiology.scans');

    // ─── Orthodontics ─────────────────────────────────────────────────────────
    Route::resource('orthodontics/cases',    \App\Http\Controllers\Orthodontics\OrthodonticCaseController::class)->names('orthodontics.cases');
    Route::put('/orthodontics/cases/{id}/restore', [\App\Http\Controllers\Orthodontics\OrthodonticCaseController::class, 'restore'])->name('orthodontics.cases.restore');
    Route::resource('orthodontics/sessions', \App\Http\Controllers\Orthodontics\OrthodonticSessionController::class)->names('orthodontics.sessions');
    Route::put('/orthodontics/sessions/{id}/restore', [\App\Http\Controllers\Orthodontics\OrthodonticSessionController::class, 'restore'])->name('orthodontics.sessions.restore');

    // ─── Reports ──────────────────────────────────────────────────────────────
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Reports\ReportController::class, 'index'])->name('index');
    });

});

require __DIR__.'/auth.php';
