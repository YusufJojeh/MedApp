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
        Schema::create('working_hours', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained('doctors')->onDelete('cascade');
            $table->integer('day_of_week'); // 0 = Sunday, 1 = Monday, etc.
            $table->time('start_time');
            $table->time('end_time');
            $table->boolean('is_available')->default(true);

            $table->unique(['doctor_id', 'day_of_week'], 'unique_doctor_day');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('working_hours');
    }
};
