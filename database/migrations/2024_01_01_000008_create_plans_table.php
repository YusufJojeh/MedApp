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
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 100)->unique();
            $table->enum('audience', ['doctor', 'patient']);
            $table->decimal('price', 10, 2);
            $table->string('currency', 10)->default('SAR');
            $table->enum('billing_cycle', ['monthly', 'yearly', 'one_time']);
            $table->boolean('is_popular')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index('audience', 'idx_audience');
            $table->index('slug', 'idx_slug');
            $table->index('sort_order', 'idx_sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
