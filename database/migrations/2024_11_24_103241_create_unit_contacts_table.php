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
        Schema::create('unit_contacts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('booking_request_id'); // Foreign key to booking_requests
            $table->string('site_contact')->nullable();
            $table->string('site_contact_email')->nullable();
            $table->string('site_contact_phone')->nullable();
            $table->string('unit_number')->nullable();  // Added field to store unit number
            $table->timestamps();
    
            // Foreign key constraint
            $table->foreign('booking_request_id')->references('id')->on('booking_requests')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unit_contacts');
    }
};
