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
        Schema::create('radiology_images', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('image_path');
            $table->text('ai_analysis')->nullable();
            $table->timestamps();
            $table->softDeletes();


            // Relationship
            $table->uuid('radiology_id');
            $table->foreign('radiology_id')->references('id')->on('radiologies')->onDelete('restrict');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('radiology_images');
    }
};
