<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Models\Appointment;
use App\Models\Payment;
use App\Models\WalletTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class NotificationService
{
    /**
     * Create a notification for a user
     */
    public function createNotification($userId, $type, $title, $message, $options = [])
    {
        $defaults = [
            'icon' => $this->getDefaultIcon($type),
            'color' => $this->getDefaultColor($type),
            'priority' => 'normal',
            'channel' => 'database',
            'data' => [],
        ];

        $options = array_merge($defaults, $options);

        return Notification::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'icon' => $options['icon'],
            'color' => $options['color'],
            'priority' => $options['priority'],
            'channel' => $options['channel'],
            'data' => $options['data'],
        ]);
    }

    /**
     * Appointment Events
     */
    public function appointmentBooked($appointmentId)
    {
        $appointment = Appointment::with(['patient.user', 'doctor.user'])->find($appointmentId);

        if (!$appointment) return;

        // Notify patient
        $this->createNotification(
            $appointment->patient->user_id,
            'appointment',
            'Appointment Booked Successfully',
            "Your appointment with Dr. {$appointment->doctor->name} has been booked for " .
            Carbon::parse($appointment->appointment_date)->format('M d, Y') .
            " at " . Carbon::parse($appointment->appointment_time)->format('g:i A'),
            [
                'icon' => '📅',
                'color' => 'green',
                'priority' => 'high',
                'data' => ['appointment_id' => $appointmentId]
            ]
        );

        // Notify doctor
        $this->createNotification(
            $appointment->doctor->user_id,
            'appointment',
            'New Appointment Request',
            "New appointment request from {$appointment->patient->user->name} for " .
            Carbon::parse($appointment->appointment_date)->format('M d, Y') .
            " at " . Carbon::parse($appointment->appointment_time)->format('g:i A'),
            [
                'icon' => '👨‍⚕️',
                'color' => 'blue',
                'priority' => 'high',
                'data' => ['appointment_id' => $appointmentId]
            ]
        );
    }

    public function appointmentConfirmed($appointmentId)
    {
        $appointment = Appointment::with(['patient.user', 'doctor.user'])->find($appointmentId);

        if (!$appointment) return;

        $this->createNotification(
            $appointment->patient->user_id,
            'appointment',
            'Appointment Confirmed',
            "Your appointment with Dr. {$appointment->doctor->name} has been confirmed for " .
            Carbon::parse($appointment->appointment_date)->format('M d, Y') .
            " at " . Carbon::parse($appointment->appointment_time)->format('g:i A'),
            [
                'icon' => '✅',
                'color' => 'green',
                'priority' => 'high',
                'data' => ['appointment_id' => $appointmentId]
            ]
        );
    }

    public function appointmentCancelled($appointmentId, $cancelledBy = 'system')
    {
        $appointment = Appointment::with(['patient.user', 'doctor.user'])->find($appointmentId);

        if (!$appointment) return;

        $cancelledByText = $cancelledBy === 'patient' ? 'You' : 'The doctor';

        // Notify patient
        $this->createNotification(
            $appointment->patient->user_id,
            'appointment',
            'Appointment Cancelled',
            "Your appointment with Dr. {$appointment->doctor->name} scheduled for " .
            Carbon::parse($appointment->appointment_date)->format('M d, Y') .
            " at " . Carbon::parse($appointment->appointment_time)->format('g:i A') .
            " has been cancelled by {$cancelledByText}.",
            [
                'icon' => '❌',
                'color' => 'red',
                'priority' => 'high',
                'data' => ['appointment_id' => $appointmentId]
            ]
        );

        // Notify doctor
        $this->createNotification(
            $appointment->doctor->user_id,
            'appointment',
            'Appointment Cancelled',
            "Appointment with {$appointment->patient->user->name} scheduled for " .
            Carbon::parse($appointment->appointment_date)->format('M d, Y') .
            " at " . Carbon::parse($appointment->appointment_time)->format('g:i A') .
            " has been cancelled.",
            [
                'icon' => '❌',
                'color' => 'red',
                'priority' => 'normal',
                'data' => ['appointment_id' => $appointmentId]
            ]
        );
    }

    public function appointmentRescheduled($appointmentId)
    {
        $appointment = Appointment::with(['patient.user', 'doctor.user'])->find($appointmentId);

        if (!$appointment) return;

        // Notify patient
        $this->createNotification(
            $appointment->patient->user_id,
            'appointment',
            'Appointment Rescheduled',
            "Your appointment with Dr. {$appointment->doctor->name} has been rescheduled to " .
            Carbon::parse($appointment->appointment_date)->format('M d, Y') .
            " at " . Carbon::parse($appointment->appointment_time)->format('g:i A'),
            [
                'icon' => '🔄',
                'color' => 'yellow',
                'priority' => 'high',
                'data' => ['appointment_id' => $appointmentId]
            ]
        );

        // Notify doctor
        $this->createNotification(
            $appointment->doctor->user_id,
            'appointment',
            'Appointment Rescheduled',
            "Appointment with {$appointment->patient->user->name} has been rescheduled to " .
            Carbon::parse($appointment->appointment_date)->format('M d, Y') .
            " at " . Carbon::parse($appointment->appointment_time)->format('g:i A'),
            [
                'icon' => '🔄',
                'color' => 'yellow',
                'priority' => 'normal',
                'data' => ['appointment_id' => $appointmentId]
            ]
        );
    }

    public function appointmentReminder($appointmentId)
    {
        $appointment = Appointment::with(['patient.user', 'doctor.user'])->find($appointmentId);

        if (!$appointment) return;

        // Notify patient
        $this->createNotification(
            $appointment->patient->user_id,
            'appointment',
            'Appointment Reminder',
            "Reminder: You have an appointment with Dr. {$appointment->doctor->name} tomorrow at " .
            Carbon::parse($appointment->appointment_time)->format('g:i A'),
            [
                'icon' => '⏰',
                'color' => 'blue',
                'priority' => 'high',
                'data' => ['appointment_id' => $appointmentId]
            ]
        );

        // Notify doctor
        $this->createNotification(
            $appointment->doctor->user_id,
            'appointment',
            'Appointment Reminder',
            "Reminder: You have an appointment with {$appointment->patient->user->name} tomorrow at " .
            Carbon::parse($appointment->appointment_time)->format('g:i A'),
            [
                'icon' => '⏰',
                'color' => 'blue',
                'priority' => 'normal',
                'data' => ['appointment_id' => $appointmentId]
            ]
        );
    }

    public function appointmentCompleted($appointmentId)
    {
        $appointment = Appointment::with(['patient.user', 'doctor.user'])->find($appointmentId);

        if (!$appointment) return;

        // Notify patient
        $this->createNotification(
            $appointment->patient->user_id,
            'appointment',
            'Appointment Completed',
            "Your appointment with Dr. {$appointment->doctor->name} has been completed. " .
            "Please check your medical records for updates.",
            [
                'icon' => '✅',
                'color' => 'green',
                'priority' => 'normal',
                'data' => ['appointment_id' => $appointmentId]
            ]
        );

        // Notify doctor
        $this->createNotification(
            $appointment->doctor->user_id,
            'appointment',
            'Appointment Completed',
            "Your appointment with {$appointment->patient->user->name} has been completed. " .
            "Please update the patient's medical records.",
            [
                'icon' => '✅',
                'color' => 'green',
                'priority' => 'normal',
                'data' => ['appointment_id' => $appointmentId]
            ]
        );
    }

    /**
     * Payment Events
     */
    public function paymentSuccessful($paymentId)
    {
        $payment = Payment::with(['user', 'appointment.doctor'])->find($paymentId);

        if (!$payment) return;

        $this->createNotification(
            $payment->user_id,
            'payment',
            'Payment Successful',
            "Payment of $" . number_format($payment->amount, 2) . " for appointment with " .
            ($payment->appointment ? "Dr. {$payment->appointment->doctor->name}" : "your appointment") .
            " has been processed successfully.",
            [
                'icon' => '💰',
                'color' => 'green',
                'priority' => 'high',
                'data' => ['payment_id' => $paymentId]
            ]
        );
    }

    public function paymentFailed($paymentId)
    {
        $payment = Payment::with(['user', 'appointment.doctor'])->find($paymentId);

        if (!$payment) return;

        $this->createNotification(
            $payment->user_id,
            'payment',
            'Payment Failed',
            "Payment of $" . number_format($payment->amount, 2) . " for appointment with " .
            ($payment->appointment ? "Dr. {$payment->appointment->doctor->name}" : "your appointment") .
            " has failed. Please try again or contact support.",
            [
                'icon' => '❌',
                'color' => 'red',
                'priority' => 'urgent',
                'data' => ['payment_id' => $paymentId]
            ]
        );
    }

    public function paymentRefunded($paymentId)
    {
        $payment = Payment::with(['user', 'appointment.doctor'])->find($paymentId);

        if (!$payment) return;

        $this->createNotification(
            $payment->user_id,
            'payment',
            'Payment Refunded',
            "A refund of $" . number_format($payment->refunded_amount, 2) . " has been processed for your payment. " .
            "The refund will appear in your account within 3-5 business days.",
            [
                'icon' => '💸',
                'color' => 'blue',
                'priority' => 'normal',
                'data' => ['payment_id' => $paymentId]
            ]
        );
    }

    /**
     * Wallet Events
     */
    public function walletTransactionCompleted($transactionId)
    {
        $transaction = WalletTransaction::with(['wallet.user'])->find($transactionId);

        if (!$transaction) return;

        $type = $transaction->amount > 0 ? 'credited' : 'debited';
        $amount = abs($transaction->amount);

        $this->createNotification(
            $transaction->wallet->user_id,
            'wallet',
            'Wallet Transaction',
            "Your wallet has been {$type} with $" . number_format($amount, 2) . ". " .
            "New balance: $" . number_format($transaction->balance_after, 2),
            [
                'icon' => $transaction->amount > 0 ? '➕' : '➖',
                'color' => $transaction->amount > 0 ? 'green' : 'red',
                'priority' => 'normal',
                'data' => ['transaction_id' => $transactionId]
            ]
        );
    }

    public function walletTransactionFailed($transactionId)
    {
        $transaction = WalletTransaction::with(['wallet.user'])->find($transactionId);

        if (!$transaction) return;

        $this->createNotification(
            $transaction->wallet->user_id,
            'wallet',
            'Transaction Failed',
            "Your wallet transaction of $" . number_format(abs($transaction->amount), 2) .
            " has failed. Please try again or contact support.",
            [
                'icon' => '❌',
                'color' => 'red',
                'priority' => 'high',
                'data' => ['transaction_id' => $transactionId]
            ]
        );
    }

    /**
     * User Management Events
     */
    public function userRegistered($userId)
    {
        $user = User::find($userId);

        if (!$user) return;

        $this->createNotification(
            $userId,
            'system',
            'Welcome to Medical Booking System',
            "Welcome {$user->name}! Your account has been created successfully. " .
            "Please complete your profile to start booking appointments.",
            [
                'icon' => '🎉',
                'color' => 'green',
                'priority' => 'normal',
                'data' => ['user_id' => $userId]
            ]
        );

        // Notify admins
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            $this->createNotification(
                $admin->id,
                'system',
                'New User Registration',
                "New user {$user->name} ({$user->email}) has registered with role: {$user->role}",
                [
                    'icon' => '👤',
                    'color' => 'blue',
                    'priority' => 'low',
                    'data' => ['new_user_id' => $userId]
                ]
            );
        }
    }

    public function profileUpdated($userId)
    {
        $user = User::find($userId);

        if (!$user) return;

        $this->createNotification(
            $userId,
            'system',
            'Profile Updated',
            "Your profile has been updated successfully.",
            [
                'icon' => '📝',
                'color' => 'blue',
                'priority' => 'low',
                'data' => ['user_id' => $userId]
            ]
        );
    }

    public function accountActivated($userId)
    {
        $user = User::find($userId);

        if (!$user) return;

        $this->createNotification(
            $userId,
            'system',
            'Account Activated',
            "Your account has been activated. You can now access all features.",
            [
                'icon' => '✅',
                'color' => 'green',
                'priority' => 'normal',
                'data' => ['user_id' => $userId]
            ]
        );
    }

    public function accountDeactivated($userId)
    {
        $user = User::find($userId);

        if (!$user) return;

        $this->createNotification(
            $userId,
            'system',
            'Account Deactivated',
            "Your account has been deactivated. Please contact support for assistance.",
            [
                'icon' => '⚠️',
                'color' => 'red',
                'priority' => 'urgent',
                'data' => ['user_id' => $userId]
            ]
        );
    }

    /**
     * Doctor-Specific Events
     */
    public function newReviewSubmitted($reviewId)
    {
        $review = DB::table('reviews')->where('id', $reviewId)->first();
        if (!$review) return;

        $appointment = DB::table('appointments')->where('id', $review->appointment_id)->first();
        if (!$appointment) return;

        $doctor = DB::table('doctors')->where('id', $appointment->doctor_id)->first();
        if (!$doctor) return;

        $this->createNotification(
            $doctor->user_id,
            'review',
            'New Review Received',
            'You have received a new review from a patient.',
            [
                'icon' => '⭐',
                'color' => 'yellow',
                'priority' => 'normal',
                'data' => [
                    'review_id' => $reviewId,
                    'appointment_id' => $review->appointment_id,
                    'rating' => $review->rating
                ]
            ]
        );
    }

    public function scheduleUpdated($doctorId)
    {
        $doctor = DB::table('doctors')->where('id', $doctorId)->first();

        if (!$doctor) return;

        $this->createNotification(
            $doctor->user_id,
            'schedule',
            'Schedule Updated',
            "Your working schedule has been updated. Please review the changes.",
            [
                'icon' => '📅',
                'color' => 'blue',
                'priority' => 'normal',
                'data' => ['doctor_id' => $doctorId]
            ]
        );
    }

    /**
     * Send notification for prescription ready
     */
    public function prescriptionReady($prescriptionId)
    {
        $prescription = DB::table('prescriptions')->where('id', $prescriptionId)->first();
        if (!$prescription) return;

        $appointment = DB::table('appointments')->where('id', $prescription->appointment_id)->first();
        if (!$appointment) return;

        $patient = DB::table('patients')->where('id', $appointment->patient_id)->first();
        if (!$patient) return;

        $this->createNotification(
            $patient->user_id,
            'prescription',
            'Prescription Ready',
            'Your prescription is ready for pickup.',
            [
                'icon' => '💊',
                'color' => 'blue',
                'priority' => 'normal',
                'data' => [
                    'prescription_id' => $prescriptionId,
                    'appointment_id' => $prescription->appointment_id
                ]
            ]
        );
    }

    /**
     * Send notification for test results available
     */
    public function testResultsAvailable($testResultId)
    {
        $testResult = DB::table('test_results')->where('id', $testResultId)->first();
        if (!$testResult) return;

        $appointment = DB::table('appointments')->where('id', $testResult->appointment_id)->first();
        if (!$appointment) return;

        $patient = DB::table('patients')->where('id', $appointment->patient_id)->first();
        if (!$patient) return;

        $this->createNotification(
            $patient->user_id,
            'test_result',
            'Test Results Available',
            'Your test results are now available.',
            [
                'icon' => '🔬',
                'color' => 'green',
                'priority' => 'normal',
                'data' => [
                    'test_result_id' => $testResultId,
                    'appointment_id' => $testResult->appointment_id
                ]
            ]
        );
    }

    /**
     * Send notification for no-show appointment
     */
    public function noShowAppointment($appointmentId)
    {
        $appointment = DB::table('appointments')->where('id', $appointmentId)->first();
        if (!$appointment) return;

        $patient = DB::table('patients')->where('id', $appointment->patient_id)->first();
        if (!$patient) return;

        $doctor = DB::table('doctors')->where('id', $appointment->doctor_id)->first();
        if (!$doctor) return;

        // Notify patient
        $this->createNotification(
            $patient->user_id,
            'appointment',
            'Missed Appointment',
            'You missed your scheduled appointment. Please reschedule if needed.',
            [
                'icon' => '❌',
                'color' => 'red',
                'priority' => 'high',
                'data' => [
                    'appointment_id' => $appointmentId,
                    'doctor_name' => $doctor->name
                ]
            ]
        );

        // Notify doctor
        $this->createNotification(
            $doctor->user_id,
            'appointment',
            'Patient No-Show',
            'A patient did not show up for their scheduled appointment.',
            [
                'icon' => '❌',
                'color' => 'red',
                'priority' => 'normal',
                'data' => [
                    'appointment_id' => $appointmentId,
                    'patient_name' => $patient->NAME
                ]
            ]
        );
    }

    /**
     * Send notification for payment pending
     */
    public function paymentPending($paymentId)
    {
        $payment = DB::table('payments')->where('id', $paymentId)->first();
        if (!$payment) return;

        $appointment = DB::table('appointments')->where('id', $payment->appointment_id)->first();
        if (!$appointment) return;

        $patient = DB::table('patients')->where('id', $appointment->patient_id)->first();
        if (!$patient) return;

        $this->createNotification(
            $patient->user_id,
            'payment',
            'Payment Pending',
            'Your payment is pending. Please complete the payment to confirm your appointment.',
            [
                'icon' => '⏳',
                'color' => 'yellow',
                'priority' => 'high',
                'data' => [
                    'payment_id' => $paymentId,
                    'amount' => $payment->amount
                ]
            ]
        );
    }

    /**
     * Send notification for payment method added/updated
     */
    public function paymentMethodUpdated($paymentMethodId, $action = 'added')
    {
        $paymentMethod = DB::table('payment_methods')->where('id', $paymentMethodId)->first();
        if (!$paymentMethod) return;

        $actionText = $action === 'added' ? 'added' : 'updated';
        $actionIcon = $action === 'added' ? '➕' : '✏️';

        $this->createNotification(
            $paymentMethod->user_id,
            'payment_method',
            'Payment Method ' . ucfirst($actionText),
            'Your payment method has been ' . $actionText . ' successfully.',
            [
                'icon' => $actionIcon,
                'color' => 'green',
                'priority' => 'normal',
                'data' => [
                    'payment_method_id' => $paymentMethodId,
                    'type' => $paymentMethod->type,
                    'action' => $action
                ]
            ]
        );
    }

    /**
     * Send notification for system maintenance
     */
    public function systemMaintenance($message, $scheduledTime = null)
    {
        $users = DB::table('users')->where('is_active', true)->get();

        foreach ($users as $user) {
            $this->createNotification(
                $user->id,
                'system',
                'System Maintenance',
                $message,
                [
                    'icon' => '🔧',
                    'color' => 'yellow',
                    'priority' => 'high',
                    'data' => [
                        'scheduled_time' => $scheduledTime,
                        'maintenance_type' => 'scheduled'
                    ]
                ]
            );
        }
    }

    /**
     * Send notification for payment dispute
     */
    public function paymentDispute($disputeId)
    {
        $dispute = DB::table('payment_disputes')->where('id', $disputeId)->first();
        if (!$dispute) return;

        // Notify admin
        $admins = DB::table('users')->where('role', 'admin')->where('is_active', true)->get();

        foreach ($admins as $admin) {
            $this->createNotification(
                $admin->id,
                'dispute',
                'Payment Dispute Filed',
                'A new payment dispute has been filed and requires attention.',
                [
                    'icon' => '⚠️',
                    'color' => 'red',
                    'priority' => 'urgent',
                    'data' => [
                        'dispute_id' => $disputeId,
                        'payment_id' => $dispute->payment_id,
                        'reason' => $dispute->reason
                    ]
                ]
            );
        }
    }

    /**
     * Send notification for follow-up appointment scheduled
     */
    public function followUpAppointmentScheduled($appointmentId)
    {
        $appointment = DB::table('appointments')->where('id', $appointmentId)->first();
        if (!$appointment) return;

        $patient = DB::table('patients')->where('id', $appointment->patient_id)->first();
        if (!$patient) return;

        $doctor = DB::table('doctors')->where('id', $appointment->doctor_id)->first();
        if (!$doctor) return;

        $this->createNotification(
            $patient->user_id,
            'appointment',
            'Follow-up Appointment Scheduled',
            'Your follow-up appointment has been scheduled successfully.',
            [
                'icon' => '🔄',
                'color' => 'blue',
                'priority' => 'normal',
                'data' => [
                    'appointment_id' => $appointmentId,
                    'doctor_name' => $doctor->name,
                    'appointment_type' => 'follow_up'
                ]
            ]
        );
    }

    /**
     * System Events
     */
    public function securityAlert($userId, $message)
    {
        $this->createNotification(
            $userId,
            'security',
            'Security Alert',
            $message,
            [
                'icon' => '🔒',
                'color' => 'red',
                'priority' => 'urgent',
                'data' => ['security_alert' => true]
            ]
        );
    }

    /**
     * Helper methods
     */
    private function getDefaultIcon($type)
    {
        return match($type) {
            'appointment' => '📅',
            'payment' => '💰',
            'wallet' => '💳',
            'review' => '⭐',
            'schedule' => '📅',
            'security' => '🔒',
            'system' => '⚙️',
            default => '📢',
        };
    }

    private function getDefaultColor($type)
    {
        return match($type) {
            'appointment' => 'blue',
            'payment' => 'green',
            'wallet' => 'blue',
            'review' => 'yellow',
            'schedule' => 'blue',
            'security' => 'red',
            'system' => 'gray',
            default => 'blue',
        };
    }

    /**
     * Send scheduled notifications (for reminders, etc.)
     */
    public function sendScheduledNotifications()
    {
        // Send appointment reminders for tomorrow
        $tomorrow = Carbon::tomorrow();
        $appointments = Appointment::with(['patient.user', 'doctor.user'])
            ->where('appointment_date', $tomorrow)
            ->where('status', 'scheduled')
            ->get();

        foreach ($appointments as $appointment) {
            $this->appointmentReminder($appointment->id);
        }
    }

    /**
     * Mark notifications as sent for external channels
     */
    public function markNotificationsAsSent($notificationIds)
    {
        Notification::whereIn('id', $notificationIds)->update([
            'is_sent' => true,
            'sent_at' => Carbon::now()
        ]);
    }
}
