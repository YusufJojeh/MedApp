<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'appointment_id',
        'amount',
        'currency',
        'payment_method',
        'payment_gateway',
        'transaction_id',
        'status',
        'gateway_response',
        'gateway_fee',
        'refunded_amount',
        'refunded_at',
        'refund_reason',
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'gateway_fee' => 'decimal:2',
        'refunded_amount' => 'decimal:2',
        'refunded_at' => 'datetime',
        'metadata' => 'array',
    ];

    /**
     * Get the user that owns the payment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the appointment that owns the payment.
     */
    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    /**
     * Scope a query to only include successful payments.
     */
    public function scopeSuccessful($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope a query to only include pending payments.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include failed payments.
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope a query to only include refunded payments.
     */
    public function scopeRefunded($query)
    {
        return $query->where('status', 'refunded');
    }

    /**
     * Get the payment status badge class.
     */
    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            'completed' => 'bg-green-100 text-green-800',
            'pending' => 'bg-yellow-100 text-yellow-800',
            'failed' => 'bg-red-100 text-red-800',
            'refunded' => 'bg-gray-100 text-gray-800',
            'cancelled' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Get the payment status text.
     */
    public function getStatusTextAttribute()
    {
        return ucfirst($this->status);
    }

    /**
     * Check if payment is successful.
     */
    public function getIsSuccessfulAttribute()
    {
        return $this->status === 'completed';
    }

    /**
     * Check if payment is pending.
     */
    public function getIsPendingAttribute()
    {
        return $this->status === 'pending';
    }

    /**
     * Check if payment is failed.
     */
    public function getIsFailedAttribute()
    {
        return $this->status === 'failed';
    }

    /**
     * Check if payment is refunded.
     */
    public function getIsRefundedAttribute()
    {
        return $this->status === 'refunded';
    }

    /**
     * Get the net amount after gateway fees.
     */
    public function getNetAmountAttribute()
    {
        return $this->amount - ($this->gateway_fee ?? 0);
    }

    /**
     * Get the remaining refundable amount.
     */
    public function getRefundableAmountAttribute()
    {
        return $this->amount - ($this->refunded_amount ?? 0);
    }

    /**
     * Check if payment can be refunded.
     */
    public function getCanBeRefundedAttribute()
    {
        return $this->status === 'completed' && $this->refundable_amount > 0;
    }
}
