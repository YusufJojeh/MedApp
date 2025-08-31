<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WalletTransaction extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'wallet_id',
        'type',
        'amount',
        'description',
        'status',
        'metadata',
        'balance_before',
        'balance_after',
        'reference_id',
        'reference_type',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'balance_before' => 'decimal:2',
        'balance_after' => 'decimal:2',
        'metadata' => 'array',
    ];

    /**
     * Get the wallet that owns the transaction.
     */
    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    /**
     * Get the user that owns the transaction.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'wallet_id', 'id');
    }

    /**
     * Scope a query to only include completed transactions.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope a query to only include pending transactions.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include failed transactions.
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope a query to only include transactions by type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Get the transaction type text.
     */
    public function getTypeTextAttribute()
    {
        return ucfirst(str_replace('_', ' ', $this->type));
    }

    /**
     * Get the transaction status badge class.
     */
    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            'completed' => 'bg-green-100 text-green-800',
            'pending' => 'bg-yellow-100 text-yellow-800',
            'failed' => 'bg-red-100 text-red-800',
            'cancelled' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Get the transaction status text.
     */
    public function getStatusTextAttribute()
    {
        return ucfirst($this->status);
    }

    /**
     * Get the transaction amount formatted.
     */
    public function getFormattedAmountAttribute()
    {
        $prefix = $this->amount >= 0 ? '+' : '';
        return $prefix . '$' . number_format(abs($this->amount), 2);
    }

    /**
     * Get the transaction balance before formatted.
     */
    public function getFormattedBalanceBeforeAttribute()
    {
        return '$' . number_format($this->balance_before, 2);
    }

    /**
     * Get the transaction balance after formatted.
     */
    public function getFormattedBalanceAfterAttribute()
    {
        return '$' . number_format($this->balance_after, 2);
    }

    /**
     * Check if transaction is a credit.
     */
    public function getIsCreditAttribute()
    {
        return $this->amount > 0;
    }

    /**
     * Check if transaction is a debit.
     */
    public function getIsDebitAttribute()
    {
        return $this->amount < 0;
    }

    /**
     * Check if transaction is successful.
     */
    public function getIsSuccessfulAttribute()
    {
        return $this->status === 'completed';
    }

    /**
     * Check if transaction is pending.
     */
    public function getIsPendingAttribute()
    {
        return $this->status === 'pending';
    }

    /**
     * Check if transaction is failed.
     */
    public function getIsFailedAttribute()
    {
        return $this->status === 'failed';
    }

    /**
     * Get the transaction date formatted.
     */
    public function getFormattedDateAttribute()
    {
        return $this->created_at->format('M d, Y');
    }

    /**
     * Get the transaction time formatted.
     */
    public function getFormattedTimeAttribute()
    {
        return $this->created_at->format('H:i');
    }

    /**
     * Get the transaction time ago.
     */
    public function getTimeAgoAttribute()
    {
        return $this->created_at->diffForHumans();
    }
}
