<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('app_settings', function (Blueprint $table) {
            $table->id();
            $table->string('app_name'); // Name of the application
            $table->string('company_address'); // Main address
            $table->string('company_city'); // Main city
            $table->string('company_state'); // Main state
            $table->string('company_zip'); // Main ZIP code
            $table->string('company_address_ny')->nullable(); // NY address
            $table->string('company_city_ny')->nullable(); // NY city
            $table->string('company_state_ny')->nullable(); // NY state
            $table->string('company_zip_ny')->nullable(); // NY ZIP code
            $table->string('company_email'); // Contact email
            $table->string('company_phone'); // Contact phone
            $table->string('company_zelle')->nullable(); // Zelle email
            $table->string('company_njdca'); // NJDCA number
            $table->string('company_owner'); // Owner name
            $table->string('stripe_public_key'); // Stripe public key
            $table->string('stripe_secret_key'); // Stripe secret key
            $table->string('vite_stripe_key'); // Vite Stripe key
            $table->string('stripe_webhook_secret')->nullable(); // Webhook secret
            $table->timestamps(); // Created and updated timestamps
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_settings');
    }
};
