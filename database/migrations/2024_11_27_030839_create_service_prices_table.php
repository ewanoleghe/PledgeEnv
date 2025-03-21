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
        Schema::create('service_prices', function (Blueprint $table) {
            $table->id(); // Auto-incrementing primary key
            $table->string('service_name')->unique(); // Name of the service
            $table->decimal('price', 10, 2); // Price with 2 decimal points
            $table->decimal('price_visual', 10, 2); // Price with 2 decimal points
            $table->decimal('price_dust', 10, 2); // Price with 2 decimal points
            $table->text('xrf', 10, 2)->nullable(); // XRF Fee
            $table->text('xrf_reinspection_price', 10, 2)->nullable(); // Optional Reinspection Price
            $table->text('visual_reinspection_price', 10, 2)->nullable(); // Optional Reinspection Price Visual Inspection
            $table->text('dust_wipe_reinspection_price', 10, 2)->nullable(); // Optional Reinspection Price Dust Wipe Sampling
            $table->text('weekendFee', 10, 2)->nullable(); // Weekend fee
            $table->text('description')->nullable(); // Optional description for the service
            $table->text('bookMax')->nullable(); // Optional description for the service
            $table->timestamps(); // Timestamps for created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_prices');
    }
};
