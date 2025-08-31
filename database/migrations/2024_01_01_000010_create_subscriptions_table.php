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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('plan_id')->constrained('plans')->onDelete('cascade');
            $table->enum('STATUS', ['active', 'canceled', 'past_due', 'trial'])->default('active');
            $table->datetime('renews_at')->nullable();
            $table->datetime('canceled_at')->nullable();
            $table->enum('provider', ['stripe', 'paypal', 'manual'])->default('stripe');
            $table->string('provider_subscription_id', 255)->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index('user_id', 'idx_user_id');
            $table->index('STATUS', 'idx_status');
            $table->index('provider_subscription_id', 'idx_provider_subscription_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
