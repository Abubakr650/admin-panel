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
        Schema::create('orthodontic_sessions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->date('session_date')->nullable();
            $table->text('treatment')->nullable();
            $table->text('teeth_changes')->nullable();
            $table->text('gum_changes')->nullable();
            $table->string('wire_type_upper')->nullable();
            $table->string('wire_type_lower')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Relationship
            $table->uuid('case_id');
            $table->foreign('case_id')->references('id')->on('orthodontic_cases')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orthodontic_sessions');
    }
};
