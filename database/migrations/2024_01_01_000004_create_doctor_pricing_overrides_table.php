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
        Schema::create('doctor_pricing_overrides', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained('doctors')->onDelete('cascade');
            $table->decimal('override_price', 10, 2);
            $table->string('currency', 10)->default('SAR');
            $table->string('note', 255)->nullable();
            $table->timestamps();

            $table->unique('doctor_id', 'unique_doctor_pricing');
            $table->index('doctor_id', 'idx_doctor_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctor_pricing_overrides');
    }
};
