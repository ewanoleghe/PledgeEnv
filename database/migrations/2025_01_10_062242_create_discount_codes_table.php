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
        Schema::create('discount_codes', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->string('code')->unique(); // Discount code (unique)
            $table->string('inspector_name'); // Inspector's name
            $table->decimal('discount_percentage', 5, 2); // Discount percentage, e.g., 15.00 for 15%
            $table->boolean('is_active')->default(true); // Whether the discount code is active
            $table->date('expiration_date')->nullable(); // Expiration date of the discount code
            $table->unsignedInteger('usage_limit')->nullable(); // Max times this code can be used
            $table->unsignedInteger('used_count')->default(0); // How many times the code has been used
            $table->timestamps(); // Created at and updated at timestamps
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discount_codes');
    }
};
