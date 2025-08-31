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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('doctor_id')->constrained('doctors')->onDelete('cascade');
            $table->foreignId('appointment_id')->constrained('appointments')->onDelete('cascade');
            $table->enum('provider', ['stripe', 'paypal']);
            $table->string('provider_payment_id', 255);
            $table->enum('STATUS', ['pending', 'succeeded', 'failed', 'refunded', 'canceled'])->default('pending');
            $table->decimal('amount', 10, 2);
            $table->string('currency', 10)->default('SAR');
            $table->decimal('platform_fee', 10, 2)->default(0.00);
            $table->decimal('net_amount', 10, 2)->default(0.00);
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('doctor_id');
            $table->index('appointment_id');
            $table->index('provider_payment_id', 'idx_provider_payment_id');
            $table->index('STATUS', 'idx_status');
            $table->index('created_at', 'idx_created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
