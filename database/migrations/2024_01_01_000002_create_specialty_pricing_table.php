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
        Schema::create('specialty_pricing', function (Blueprint $table) {
            $table->id();
            $table->foreignId('specialty_id')->constrained('specialties')->onDelete('cascade');
            $table->decimal('base_price', 10, 2);
            $table->string('currency', 10)->default('SAR');
            $table->string('note', 255)->nullable();
            $table->timestamps();

            $table->unique('specialty_id', 'unique_specialty_pricing');
            $table->index('specialty_id', 'idx_specialty_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('specialty_pricing');
    }
};
