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
        Schema::table('users', function (Blueprint $table) {
            // Notification channel preferences
            $table->boolean('email_notifications')->default(true);
            $table->boolean('sms_notifications')->default(false);
            $table->boolean('push_notifications')->default(true);
            
            // Notification type preferences
            $table->boolean('appointment_reminders')->default(true);
            $table->boolean('payment_notifications')->default(true);
            $table->boolean('system_notifications')->default(true);
            
            // Notification settings
            $table->integer('appointment_reminder_hours')->default(24);
            $table->string('notification_timezone')->default('UTC');
            $table->time('quiet_hours_start')->nullable();
            $table->time('quiet_hours_end')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'email_notifications',
                'sms_notifications',
                'push_notifications',
                'appointment_reminders',
                'payment_notifications',
                'system_notifications',
                'appointment_reminder_hours',
                'notification_timezone',
                'quiet_hours_start',
                'quiet_hours_end'
            ]);
        });
    }
};
