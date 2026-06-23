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
        Schema::create('pharmacy_batches', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('batch_number');
            $table->integer('quantity')->default(0);
            $table->integer('remaining_quantity')->default(0);
            $table->date('production_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Relationship
            $table->uuid('pharmacy_item_id');   
            $table->foreign('pharmacy_item_id')->references('id')->on('pharmacy_items')->onDelete('restrict');
            
            $table->uuid('supplier_id');   
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pharmacy_batches');
    }
};
