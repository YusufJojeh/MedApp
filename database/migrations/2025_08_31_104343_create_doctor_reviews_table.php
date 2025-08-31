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
        Schema::create('doctor_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('doctor_id')->constrained('doctors')->onDelete('cascade');
            $table->foreignId('appointment_id')->nullable()->constrained('appointments')->onDelete('set null');
            $table->integer('rating')->comment('Rating from 1 to 5');
            $table->text('comment')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->timestamps();

            // Ensure a patient can only review a doctor once per appointment
            $table->unique(['patient_id', 'doctor_id', 'appointment_id']);

            // Add indexes for better performance
            $table->index(['doctor_id', 'rating']);
            $table->index(['patient_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctor_reviews');
    }
};
