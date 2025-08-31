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
        Schema::create('plan_features', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_id')->constrained('plans')->onDelete('cascade');
            $table->string('label', 255);
            $table->boolean('is_included')->default(true);
            $table->string('note', 255)->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamp('created_at')->useCurrent();

            $table->index('plan_id', 'idx_plan_id');
            $table->index('sort_order', 'idx_sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plan_features');
    }
};
