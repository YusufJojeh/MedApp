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
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('name', 100);
            $table->foreignId('specialty_id')->constrained('specialties')->onDelete('cascade');
            $table->text('description')->nullable();
            $table->integer('experience_years')->nullable();
            $table->text('education')->nullable();
            $table->string('languages', 200)->nullable();
            $table->decimal('consultation_fee', 10, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->decimal('rating', 3, 2)->default(0.00);
            $table->integer('total_reviews')->default(0);
            $table->timestamps();

            $table->index('user_id');
            $table->index('specialty_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctors');
    }
};
