<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 0. Default Admin User (so the system can be accessed after a fresh migration without seeders)
        DB::table('users')->insertOrIgnore([
            'id' => (string) Str::uuid(),
            'name' => 'System Admin',
            'full_name' => 'System Administrator',
            'email' => 'admin@dental.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'gender' => 'male',
            'phone' => '000000000',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 1. Default Appointment Service (Consultation)
        // Insert a default appointment service so it's available in the system out of the box and can be modified.
        $existingService = DB::table('services')->where('code', 'APT-001')->first();
        if (!$existingService) {
            DB::table('services')->insert([
                'id' => (string) Str::uuid(),
                'name' => 'Consultation',
                'code' => 'APT-001',
                'default_price' => 50.00,
                'duration_minutes' => 30,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            // If the service exists (e.g. from a previous state in Arabic), just rename it to English
            DB::table('services')->where('code', 'APT-001')->update(['name' => 'Consultation']);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

    }
};
