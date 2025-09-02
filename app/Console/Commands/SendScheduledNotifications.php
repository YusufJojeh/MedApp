<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\NotificationService;
use App\Models\Appointment;
use Carbon\Carbon;

class SendScheduledNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:send-scheduled {--type=all : Type of notifications to send (all, reminders, system)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send scheduled notifications like appointment reminders';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $type = $this->option('type');
        $notificationService = app(NotificationService::class);

        $this->info('Starting scheduled notification process...');

        if ($type === 'all' || $type === 'reminders') {
            $this->sendAppointmentReminders($notificationService);
        }

        if ($type === 'all' || $type === 'system') {
            $this->sendSystemNotifications($notificationService);
        }

        $this->info('Scheduled notification process completed!');
    }

    /**
     * Send appointment reminders
     */
    private function sendAppointmentReminders(NotificationService $notificationService)
    {
        $this->info('Sending appointment reminders...');

        // Get appointments for tomorrow
        $tomorrow = Carbon::tomorrow();
        $appointments = Appointment::with(['patient.user', 'doctor.user'])
            ->where('appointment_date', $tomorrow)
            ->where('status', 'scheduled')
            ->get();

        $count = 0;
        foreach ($appointments as $appointment) {
            try {
                $notificationService->appointmentReminder($appointment->id);
                $count++;
                $this->line("Sent reminder for appointment #{$appointment->id}");
            } catch (\Exception $e) {
                $this->error("Failed to send reminder for appointment #{$appointment->id}: " . $e->getMessage());
            }
        }

        $this->info("Sent {$count} appointment reminders.");
    }

    /**
     * Send system notifications
     */
    private function sendSystemNotifications(NotificationService $notificationService)
    {
        $this->info('Sending system notifications...');

        // Example: Send daily system health check
        $users = \App\Models\User::where('is_active', true)->get();
        
        $count = 0;
        foreach ($users as $user) {
            try {
                $notificationService->createNotification(
                    $user->id,
                    'system',
                    'Daily System Update',
                    'Your medical booking system is running smoothly. All services are operational.',
                    [
                        'icon' => '✅',
                        'color' => 'green',
                        'priority' => 'low'
                    ]
                );
                $count++;
            } catch (\Exception $e) {
                $this->error("Failed to send system notification to user #{$user->id}: " . $e->getMessage());
            }
        }

        $this->info("Sent {$count} system notifications.");
    }
}
