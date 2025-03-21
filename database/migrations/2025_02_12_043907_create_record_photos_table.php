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
        Schema::create('record_photos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('record_id'); // Foreign key reference to records table
            $table->string('photo_path')->nullable(); // Store photo file path
            $table->string('description')->nullable(); // Store photo description
            $table->string('room')->nullable(); // Store photo room
            $table->string('component')->nullable(); // Store photo component
            $table->string('hazard')->nullable(); // Store photo hazard
            $table->string('substrate')->nullable(); // Store photo substrate
            $table->string('value')->nullable(); // Store photo value
            $table->timestamps();
        
            // Foreign key constraint
            $table->foreign('record_id')->references('id')->on('records')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('record_photos');
    }
};
