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
        Schema::create('payment_webhooks', function (Blueprint $table) {
            $table->id();
            $table->enum('provider', ['stripe', 'paypal']);
            $table->string('event_type', 100);
            $table->string('event_id', 255);
            $table->foreignId('payment_id')->nullable()->constrained('payments')->onDelete('set null');
            $table->json('payload');
            $table->boolean('processed')->default(false);
            $table->text('error_message')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->unique(['provider', 'event_id'], 'unique_event');
            $table->index('payment_id');
            $table->index('provider', 'idx_provider');
            $table->index('event_type', 'idx_event_type');
            $table->index('processed', 'idx_processed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_webhooks');
    }
};
