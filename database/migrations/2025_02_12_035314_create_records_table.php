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
        Schema::create('records', function (Blueprint $table) {
            $table->id();
            $table->string('certificate_number')->unique();
            $table->unsignedBigInteger('booking_id'); // Foreign key column 
            $table->string('order_id');
            $table->integer('unit_number');
            $table->text('comments')->nullable();
            $table->string('pass_fail');  //pass or failed
            $table->string('selected_date');
            $table->string('selected_time');
            $table->string('address');
            $table->string('municipality');
            $table->string('city');
            $table->string('county');
            $table->string('block');
            $table->string('lot');
            $table->string('service');
            $table->string('methodology');
            $table->boolean('includeXrf'); 
            $table->string('optional_message')->nullable();
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone');
            $table->string('property_owner_name');
            $table->string('property_owner_phone');
            $table->string('property_owner_email');
            $table->string('signature');  // assuming we're storing the path to the uploaded file
            $table->string('license_number');
            $table->string('inspector_name');
            $table->date('date_issued')->nullable();
            $table->string('designation')->nullable();
            $table->string('admin_review')->default('pending');
            $table->string('report_status')->default('pending');
            $table->string('floor_plan');
            $table->string('lab_report')->nullable();
            $table->string('chain_custody')->nullable();
            $table->string('xrf_report')->nullable();
            $table->string('xrf_csv')->nullable();
            $table->string('xrf_pass_fail')->nullable();
            $table->timestamps();
    
            // Add foreign key constraint (assuming bookings table exists)
            $table->foreign('booking_id')->references('id')->on('booking_requests')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('records');
    }
};
