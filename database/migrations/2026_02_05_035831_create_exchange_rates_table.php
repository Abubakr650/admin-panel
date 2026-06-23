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
        Schema::create('exchange_rates', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->decimal('rate',12,6);

            $table->timestamps();
            $table->softDeletes();

            // Relationship
            $table->uuid('from_currency_id');
            $table->foreign('from_currency_id')->references('id')->on('currencies')->onDelete('restrict');

            $table->uuid('to_currency_id');
            $table->foreign('to_currency_id')->references('id')->on('currencies')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exchange_rates');
    }
};
