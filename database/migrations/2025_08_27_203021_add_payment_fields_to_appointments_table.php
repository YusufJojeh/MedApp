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
        Schema::table('appointments', function (Blueprint $table) {
            $table->decimal('consultation_fee', 10, 2)->nullable()->after('appointment_time');
            $table->enum('payment_method', ['wallet', 'pay_on_site', 'card', 'cash'])->nullable()->after('consultation_fee');
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])->default('pending')->after('payment_method');
            $table->string('transaction_id')->nullable()->after('payment_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn(['consultation_fee', 'payment_method', 'payment_status', 'transaction_id']);
        });
    }
};
