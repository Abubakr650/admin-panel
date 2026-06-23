<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->date('appointment_date');
            $table->time('appointment_time');
            $table->enum('appointment_status', [
                'scheduled',
                'confirmed',
                'completed',
                'cancelled',
                'no_show'
            ])->default('scheduled');
            $table->text('appointment_notes')->nullable();
            $table->timestamps();
            $table->softDeletes();


            // Relationship
            $table->uuid('patient_id');
            $table->foreign('patient_id')->references('id')->on('patients')->onDelete('restrict');

            $table->uuid('doctor_id');
            $table->foreign('doctor_id')->references('id')->on('doctors')->onDelete('restrict');

            $table->uuid('parent_appointment_id')->nullable();
            $table->foreign('parent_appointment_id')->references('id')->on('appointments')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
