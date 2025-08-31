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
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['credit_card', 'debit_card', 'bank_account']);

            // Card information (for credit/debit cards)
            $table->string('card_brand')->nullable(); // Visa, Mastercard, etc.
            $table->string('last_four_digits', 4)->nullable();
            $table->string('expiry_month', 2)->nullable();
            $table->string('expiry_year', 4)->nullable();
            $table->string('cardholder_name')->nullable();
            $table->text('card_number_encrypted')->nullable(); // Encrypted card number
            $table->text('cvv_encrypted')->nullable(); // Encrypted CVV

            // Bank account information
            $table->string('bank_name')->nullable();
            $table->text('account_number_encrypted')->nullable(); // Encrypted account number
            $table->text('routing_number_encrypted')->nullable(); // Encrypted routing number
            $table->enum('account_type', ['checking', 'savings'])->nullable();

            // Status flags
            $table->boolean('is_default')->default(false);
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_active')->default(true);

            // Metadata
            $table->string('gateway_customer_id')->nullable(); // Payment gateway customer ID
            $table->string('gateway_payment_method_id')->nullable(); // Payment gateway method ID
            $table->json('gateway_metadata')->nullable(); // Additional gateway data

            $table->timestamps();

            // Indexes
            $table->index(['user_id', 'is_default']);
            $table->index(['user_id', 'is_active']);
            $table->index('gateway_customer_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};
