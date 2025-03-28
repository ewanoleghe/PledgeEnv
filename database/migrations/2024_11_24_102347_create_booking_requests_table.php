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
        Schema::create('booking_requests', function (Blueprint $table) {
            $table->id();
            $table->string('order_id')->unique();
            $table->string('old_order_id')->nullable();
            $table->string('service');
            $table->date('selected_date');
            $table->decimal('price', 8, 2);
            $table->decimal('price_visual', 8, 2);
            $table->decimal('price_dust', 8, 2);
            $table->decimal('xrf', 8, 2);
            $table->decimal('xrf_reinspection_price', 8, 2);
            $table->decimal('visual_reinspection_price', 8, 2);
            $table->decimal('dust_wipe_reinspection_price', 8, 2);
            $table->decimal('weekendFee', 8, 2)->nullable();
            $table->decimal('totalWeekendFee', 8, 2)->nullable();
            $table->decimal('NewSubTotal', 8, 2);
            $table->decimal('totalXrf', 8, 2);
            $table->decimal('baseXrf', 8, 2);
            $table->decimal('credSucharg', 8, 2);
            $table->decimal('totalPay', 8, 2);
            $table->decimal('basePrice', 8, 2);
            $table->decimal('totalBasePrice', 8, 2);
            $table->boolean('includeXrf');
            $table->string('InspectionType');
            $table->integer('dcaMunicode');
            $table->string('municipality');
            $table->string('county');
            $table->string('methodology');
            $table->decimal('totalCost', 8, 2)->nullable();
            $table->decimal('subtotal', 8, 2);
            $table->string('selectedTime');
            $table->string('name');
            $table->string('email');
            $table->string('phone');
            $table->string('address');
            $table->string('apt')->nullable();
            $table->string('city');
            $table->string('state');
            $table->integer('block')->nullable();
            $table->integer('lot')->nullable();
            $table->integer('units');
            $table->string('builtBefore1978');
            $table->boolean('useSameContactForAll');
            $table->string('siteContact')->nullable();
            $table->string('siteContactEmail')->nullable();
            $table->string('siteContactPhone')->nullable();
            $table->string('optionalMessage')->nullable();
            $table->text('agreedToTerms');
            $table->string('read_unr')->default('unread')->nullable();
            $table->string('assignTo')->nullable();
            $table->string('jobStatus')->nullable();
            $table->string('payment_status')->default('pending');
            $table->string('discountCode')->nullable();
            $table->decimal('discountTotal', 10, 2)->nullable();
            $table->decimal('discountPercentage', 5, 2)->nullable();
            $table->string('selectedPaymentMethod')->nullable(); 
            $table->string('payment_intent')->nullable(); // Add this line to your booking_requests table migration            
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_requests');
    }
};
