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
        Schema::create('warehouse_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('company_name')->nullable();
            $table->string('type')->nullable();               // نوع المادة
            $table->integer('quantity')->default(0);
            $table->date('production_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->enum('category', ['chemical', 'equipment', 'packaging', 'other'])
                ->default('other');           // تصنيف المادة
            $table->string('qr_code')->nullable()->unique();  // رمز QR
            $table->string('location_in_warehouse')->nullable(); // موقع المادة في المستودع
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Relationship
            $table->uuid('supplier_id');   
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouse_items');
    }
};
