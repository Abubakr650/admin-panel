<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Billing\Currency;
use App\Models\Billing\ExchangeRate;
use App\Models\Billing\Invoice;
use App\Models\Billing\InvoiceItem;
use App\Models\Billing\Payment;
use App\Models\Billing\Service;
use App\Models\Clinic\Appointment;
use App\Models\Clinic\Doctor;
use App\Models\Clinic\Patient;
use App\Models\Clinic\Treatment;
use App\Models\Pharmacy\PharmacyBatch;
use App\Models\Pharmacy\PharmacyItem;
use App\Models\Pharmacy\Supplier;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // 1. Users
        $admin = User::firstOrCreate(['email' => 'admin@dental.com'], [
            'name' => 'Dr. Ahmed Admin', 'full_name' => 'Ahmed Mohamed Ali',
            'password' => Hash::make('password'), 'role' => 'admin',
            'phone' => '777111222', 'gender' => 'male', 'email_verified_at' => now(),
        ]);

        $doctorUser = User::firstOrCreate(['email' => 'doctor@dental.com'], [
            'name' => 'Dr. Sara', 'full_name' => 'Sara Al-Odaini',
            'password' => Hash::make('password'), 'role' => 'doctor',
            'phone' => '777333444', 'gender' => 'female', 'email_verified_at' => now(),
        ]);

        $receptionist = User::firstOrCreate(['email' => 'reception@dental.com'], [
            'name' => 'Muna', 'full_name' => 'Muna Saeed',
            'password' => Hash::make('password'), 'role' => 'receptionist',
            'phone' => '777555666', 'gender' => 'female', 'email_verified_at' => now(),
        ]);

        $pharmacist = User::firstOrCreate(['email' => 'pharmacy@dental.com'], [
            'name' => 'Dr. Khalid', 'full_name' => 'Khalid Ibrahim',
            'password' => Hash::make('password'), 'role' => 'pharmacist',
            'phone' => '777777888', 'gender' => 'male', 'email_verified_at' => now(),
        ]);

        // 2. Doctor Profiles
        $doctorsList = [];
        $doctor = Doctor::firstOrCreate(['user_id' => $doctorUser->id], [
            'specialty' => 'Orthodontics', 'degree' => 'PhD in Dental Surgery', 'is_active' => true,
        ]);
        $doctorsList[] = $doctor;

        // Generate 50 more fake doctors
        for ($i = 1; $i <= 50; $i++) {
            $gender = fake()->randomElement(['male', 'female']);
            $name = fake()->firstName($gender) . ' ' . fake()->lastName();
            $u = User::firstOrCreate(['email' => "doctor{$i}@dental.com"], [
                'name' => 'Dr. ' . explode(' ', $name)[0],
                'full_name' => $name,
                'password' => Hash::make('password'),
                'role' => 'doctor',
                'phone' => fake()->phoneNumber(),
                'gender' => $gender,
                'email_verified_at' => now(),
            ]);
            $d = Doctor::firstOrCreate(['user_id' => $u->id], [
                'specialty' => fake()->randomElement(['General Dentistry', 'Orthodontics', 'Endodontics', 'Pediatric Dentistry', 'Oral Surgery', 'Prosthodontics', 'Periodontics']),
                'degree' => fake()->randomElement(['BDS', 'DDS', 'PhD', 'MSc']),
                'is_active' => fake()->boolean(90),
            ]);
            $doctorsList[] = $d;
        }

        // 3. Currencies
        $usd = Currency::firstOrCreate(['code' => 'USD'], ['name' => 'US Dollar']);
        $sar = Currency::firstOrCreate(['code' => 'SAR'], ['name' => 'Saudi Riyal']);
        $yer = Currency::firstOrCreate(['code' => 'YER'], ['name' => 'Yemeni Rial']);

        ExchangeRate::updateOrCreate(
            ['from_currency_id' => $usd->id, 'to_currency_id' => $yer->id], ['rate' => 535.00]
        );
        ExchangeRate::updateOrCreate(
            ['from_currency_id' => $sar->id, 'to_currency_id' => $yer->id], ['rate' => 142.50]
        );

        // 4. Services Catalog (with default_price)
        $servicesData = [
            ['name' => 'Dental Cleaning (Standard)',  'code' => 'CLN-001', 'default_price' => 20.00,  'type' => 'cleaning'],
            ['name' => 'Dental Cleaning (Deep)',       'code' => 'CLN-002', 'default_price' => 45.00,  'type' => 'cleaning'],
            ['name' => 'Composite Filling - Small',    'code' => 'FIL-001', 'default_price' => 35.00,  'type' => 'filling'],
            ['name' => 'Composite Filling - Large',    'code' => 'FIL-002', 'default_price' => 55.00,  'type' => 'filling'],
            ['name' => 'Simple Tooth Extraction',      'code' => 'EXT-001', 'default_price' => 25.00,  'type' => 'extraction'],
            ['name' => 'Surgical Tooth Extraction',    'code' => 'EXT-002', 'default_price' => 80.00,  'type' => 'extraction'],
            ['name' => 'Root Canal - Anterior',        'code' => 'RCT-001', 'default_price' => 120.00, 'type' => 'other'],
            ['name' => 'Root Canal - Posterior',       'code' => 'RCT-002', 'default_price' => 180.00, 'type' => 'other'],
            ['name' => 'X-Ray (Periapical)',           'code' => 'RAD-001', 'default_price' => 5.00,   'type' => 'radiology'],
            ['name' => 'X-Ray (Panorama)',             'code' => 'RAD-002', 'default_price' => 20.00,  'type' => 'radiology'],
            ['name' => 'X-Ray (CBCT Scan)',            'code' => 'RAD-003', 'default_price' => 50.00,  'type' => 'radiology'],
            ['name' => 'Orthodontic Consultation',     'code' => 'ORT-001', 'default_price' => 15.00,  'type' => 'consultation'],
            ['name' => 'Orthodontic Adjustment',       'code' => 'ORT-002', 'default_price' => 40.00,  'type' => 'orthodontic_session'],
            ['name' => 'Teeth Whitening',              'code' => 'COS-001', 'default_price' => 200.00, 'type' => 'cosmetic'],
            ['name' => 'Dental Veneers',               'code' => 'COS-002', 'default_price' => 350.00, 'type' => 'cosmetic'],
        ];

        $services = [];
        foreach ($servicesData as $srv) {
            $services[$srv['code']] = Service::firstOrCreate(
                ['code' => $srv['code']],
                ['name' => $srv['name'], 'default_price' => $srv['default_price'], 'is_active' => true]
            );
            $services[$srv['code']]->_type = $srv['type']; // temp for seeding
        }

        // 5. Pharmacy & Suppliers
        $allBatches = [];
        for ($s = 1; $s <= 30; $s++) {
            $supplier = Supplier::create([
                'name' => fake()->company(), 'phone' => fake()->phoneNumber(),
                'email' => fake()->companyEmail(), 'address' => fake()->address(),
                'country' => fake()->country(),
            ]);

            for ($i = 1; $i <= 50; $i++) {
                $defPrice = fake()->randomElement([2.00, 5.00, 8.00, 10.00, 15.00, 20.00]);
                $pItem = PharmacyItem::create([
                    'commercial_name' => fake()->word() . ' ' . fake()->randomElement(['Extra', 'Plus', 'Forte', '100mg', '500mg']),
                    'scientific_name' => fake()->word() . ' hydrochloride',
                    'company_name' => fake()->company(),
                    'form' => fake()->randomElement(['tablet', 'capsule', 'syrup', 'injection']),
                    'category' => fake()->randomElement(['medicine', 'supplement']),
                    'default_price' => $defPrice,
                ]);

                $qty = rand(50, 500);
                $batch = PharmacyBatch::create([
                    'pharmacy_item_id' => $pItem->id,
                    'batch_number' => 'B-' . strtoupper(fake()->bothify('??###')),
                    'quantity' => $qty, 'remaining_quantity' => $qty,
                    'production_date' => now()->subMonths(rand(1, 12)),
                    'expiry_date' => now()->addMonths(rand(6, 36)),
                    'supplier_id' => $supplier->id,
                ]);
                $allBatches[] = $batch;
            }
        }

        // 7. Patients, Appointments, Treatments, Invoices, Payments
        $serviceCodes = array_keys($services);
        $invoiceCounter = 1;

        for ($p = 1; $p <= 1000; $p++) {
            $patient = Patient::create([
                'full_name' => fake()->name(),
                'gender' => fake()->randomElement(['male', 'female']),
                'phone' => fake()->phoneNumber(),
                'address' => fake()->address(),
                'birth_date' => fake()->date('Y-m-d', '2010-01-01'),
            ]);

            // Appointments
            $patientAppointments = [];
            for ($a = 0; $a < rand(1, 4); $a++) {
                $isPast = rand(0, 1);
                $date = $isPast ? now()->subDays(rand(1, 365)) : now()->addDays(rand(1, 60));
                $selectedDoctor = fake()->randomElement($doctorsList);
                $apt = Appointment::create([
                    'patient_id' => $patient->id, 'doctor_id' => $selectedDoctor->id,
                    'appointment_date' => $date->format('Y-m-d'),
                    'appointment_time' => fake()->time('H:i'),
                    'appointment_status' => $isPast ? 'completed' : 'scheduled',
                    'appointment_notes' => fake()->sentence(),
                ]);
                if ($isPast) $patientAppointments[] = $apt;
            }

            // Treatments from completed appointments
            $patientTreatments = [];
            foreach ($patientAppointments as $apt) {
                for ($t = 0; $t < rand(1, 3); $t++) {
                    $code = fake()->randomElement($serviceCodes);
                    $srv = $services[$code];
                    $price = $srv->default_price;
                    $discount = fake()->randomElement([0, 0, 0, 5, 10]);
                    $total = max(0, $price - $discount);

                    $treatment = Treatment::create([
                        'patient_id' => $patient->id, 'doctor_id' => $apt->doctor_id,
                        'appointment_id' => $apt->id, 'service_id' => $srv->id,
                        'type' => $srv->_type, 'quantity' => 1,
                        'price' => $price, 'discount' => $discount, 'total' => $total,
                        'status' => Treatment::STATUS_COMPLETED,
                        'billing_status' => Treatment::BILLING_BILLED,
                        'description' => fake()->sentence(),
                        'created_by' => $apt->doctor->user_id ?? $doctorUser->id,
                    ]);
                    $patientTreatments[] = $treatment;
                }
            }

            // Pharmacy dispense for some patients
            if (rand(0, 1) && count($allBatches) > 0) {
                $batch = fake()->randomElement($allBatches);
                $qty = rand(1, 3);
                if ($batch->remaining_quantity >= $qty) {
                    $itemPrice = $batch->pharmacyItem->default_price;
                    $total = $itemPrice * $qty;

                    $treatment = Treatment::create([
                        'patient_id' => $patient->id, 'doctor_id' => null,
                        'pharmacy_batch_id' => $batch->id,
                        'type' => Treatment::TYPE_PHARMACY_DISPENSE, 'quantity' => $qty,
                        'price' => $itemPrice, 'discount' => 0, 'total' => $total,
                        'status' => Treatment::STATUS_COMPLETED,
                        'billing_status' => Treatment::BILLING_BILLED,
                        'description' => 'Dispensed: ' . $batch->pharmacyItem->commercial_name,
                        'created_by' => $pharmacist->id,
                    ]);
                    $patientTreatments[] = $treatment;
                    $batch->decrement('remaining_quantity', $qty);
                }
            }

            // Invoice + Items + Payment
            if (count($patientTreatments) > 0 && rand(0, 1)) {
                $totalAmount = collect($patientTreatments)->sum('total');
                $disc = fake()->randomElement([0, 0, 5, 10]);
                $finalAmount = round($totalAmount - ($totalAmount * $disc / 100), 2);
                $payStatus = fake()->randomElement(['paid', 'partial', 'unpaid']);

                $invoice = Invoice::create([
                    'invoice_number' => 'INV-' . str_pad($invoiceCounter++, 5, '0', STR_PAD_LEFT),
                    'patient_id' => $patient->id, 'doctor_id' => $patientTreatments[0]->doctor_id ?? null,
                    'total_amount' => $totalAmount, 'discount_percent' => $disc,
                    'final_amount' => $finalAmount, 'payment_status' => $payStatus,
                    'exchange_rate' => 1.0, 'currency_id' => $usd->id,
                    'created_by' => $receptionist->id,
                ]);

                foreach ($patientTreatments as $trmt) {
                    InvoiceItem::create([
                        'invoice_id' => $invoice->id, 'treatment_id' => $trmt->id,
                        'quantity' => $trmt->quantity,
                        'unit_price' => $trmt->price, 'total_price' => $trmt->total,
                    ]);
                }

                if ($payStatus === 'paid') {
                    Payment::create([
                        'invoice_id' => $invoice->id, 'amount' => $finalAmount,
                        'payment_method' => fake()->randomElement(['cash', 'card']),
                        'paid_at' => now()->subDays(rand(1, 30)),
                        'exchange_rate' => 1.0, 'currency_id' => $usd->id,
                        'notes' => 'Full payment', 'created_by' => $receptionist->id,
                    ]);
                } elseif ($payStatus === 'partial') {
                    Payment::create([
                        'invoice_id' => $invoice->id,
                        'amount' => round($finalAmount * rand(30, 70) / 100, 2),
                        'payment_method' => 'cash',
                        'paid_at' => now()->subDays(rand(10, 30)),
                        'exchange_rate' => 1.0, 'currency_id' => $usd->id,
                        'notes' => 'Partial payment', 'created_by' => $receptionist->id,
                    ]);
                }
            }
        }

        // 7. Radiology & Orthodontics
        $samplePatients = Patient::inRandomOrder()->limit(100)->get();
        foreach ($samplePatients as $pt) {
            $radService = Service::where('code', 'like', 'RAD-%')->inRandomOrder()->first();
            $rad = \App\Models\Radiology\Radiology::create([
                'patient_id' => $pt->id, 'doctor_id' => fake()->randomElement($doctorsList)->id,
                'service_id' => $radService->id,
                'radiology_type' => fake()->randomElement(['Panorama', 'Bitewing', 'Cephalometric']),
                'diagnosis' => fake()->paragraph(),
            ]);
            \App\Models\Radiology\RadiologyImage::create([
                'radiology_id' => $rad->id,
                'image_path' => 'scans/' . fake()->word() . '.jpg',
                'ai_analysis' => fake()->sentence(),
            ]);

            if (rand(0, 1)) {
                $oCase = \App\Models\Orthodontics\OrthodonticCase::create([
                    'patient_id' => $pt->id, 'doctor_id' => fake()->randomElement($doctorsList)->id,
                    'diagnosis' => fake()->sentence(), 'plan' => fake()->paragraph(),
                    'total_amount' => rand(1000, 3000), 'installment_amount' => 100,
                    'status' => 'active',
                ]);
                for ($s = 1; $s <= 3; $s++) {
                    \App\Models\Orthodontics\OrthodonticSession::create([
                        'case_id' => $oCase->id, 'session_date' => now()->subMonths($s),
                        'treatment' => fake()->sentence(), 'teeth_changes' => fake()->sentence(),
                        'gum_changes' => 'Normal', 'wire_type_upper' => '0.14 Niti',
                        'wire_type_lower' => '0.14 Niti',
                    ]);
                }
            }
        }
    }
}
