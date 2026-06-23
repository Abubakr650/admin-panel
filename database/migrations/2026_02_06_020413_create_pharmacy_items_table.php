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
        Schema::create('pharmacy_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('commercial_name');
            $table->string('scientific_name');
            $table->string('company_name');
            $table->enum('form', ['tablet', 'capsule', 'syrup', 'cream', 'ointment', 'injection', 'suspension', 'drops'])
            ->default('tablet');             // شكل الدواء (حبوب، شراب، إبر، إلخ)
            $table->enum('category', ['medicine', 'supplement', 'cosmetic', 'other'])
            ->default('other');           // تصنيف الدواء
            $table->decimal('default_price', 12, 2)->default(0); // السعر المرجعي للدواء
            $table->string('qr_code')->nullable()->unique();  // رمز QR
            $table->string('location_in_pharmacy')->nullable(); // موقع الدواء في الصيدلية
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pharmacy_items');
    }
};
