<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'patient_id',
        'doctor_id',
        'appointment_date',
        'appointment_time',
        'appointment_type',
        'status',
        'consultation_fee',
        'notes',
        'symptoms',
        'diagnosis',
        'prescription',
        'follow_up_date',
        'cancellation_reason',
        'cancelled_by',
        'cancelled_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'appointment_date' => 'date',
        'appointment_time' => 'datetime',
        'consultation_fee' => 'decimal:2',
        'follow_up_date' => 'date',
        'cancelled_at' => 'datetime',
    ];

    /**
     * Get the patient that owns the appointment.
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Get the doctor that owns the appointment.
     */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    /**
     * Get the payment for the appointment.
     */
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    /**
     * Scope a query to only include scheduled appointments.
     */
    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    /**
     * Scope a query to only include completed appointments.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope a query to only include cancelled appointments.
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    /**
     * Scope a query to only include upcoming appointments.
     */
    public function scopeUpcoming($query)
    {
        return $query->where('appointment_date', '>=', now()->toDateString())
                    ->where('status', 'scheduled');
    }

    /**
     * Scope a query to only include past appointments.
     */
    public function scopePast($query)
    {
        return $query->where('appointment_date', '<', now()->toDateString());
    }

    /**
     * Get the appointment status badge class.
     */
    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            'scheduled' => 'bg-blue-100 text-blue-800',
            'completed' => 'bg-green-100 text-green-800',
            'cancelled' => 'bg-red-100 text-red-800',
            'no_show' => 'bg-yellow-100 text-yellow-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Get the appointment status text.
     */
    public function getStatusTextAttribute()
    {
        return ucfirst(str_replace('_', ' ', $this->status));
    }

    /**
     * Check if appointment is upcoming.
     */
    public function getIsUpcomingAttribute()
    {
        return $this->appointment_date >= now()->toDateString() && $this->status === 'scheduled';
    }

    /**
     * Check if appointment is past.
     */
    public function getIsPastAttribute()
    {
        return $this->appointment_date < now()->toDateString();
    }

    /**
     * Check if appointment can be cancelled.
     */
    public function getCanBeCancelledAttribute()
    {
        return $this->status === 'scheduled' && $this->appointment_date > now()->addDay();
    }

    /**
     * Get the appointment datetime.
     */
    public function getAppointmentDateTimeAttribute()
    {
        return $this->appointment_date->format('Y-m-d') . ' ' . $this->appointment_time;
    }
}
