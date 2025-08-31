<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Wallet extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'balance',
        'currency',
        'is_active',
        'last_transaction_at',
        'total_deposits',
        'total_withdrawals',
        'total_earnings',
        'total_spent',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'balance' => 'decimal:2',
        'is_active' => 'boolean',
        'last_transaction_at' => 'datetime',
        'total_deposits' => 'decimal:2',
        'total_withdrawals' => 'decimal:2',
        'total_earnings' => 'decimal:2',
        'total_spent' => 'decimal:2',
    ];

    /**
     * Get the user that owns the wallet.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the transactions for the wallet.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class);
    }

    /**
     * Scope a query to only include active wallets.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include wallets with positive balance.
     */
    public function scopeWithBalance($query)
    {
        return $query->where('balance', '>', 0);
    }

    /**
     * Get the wallet balance formatted.
     */
    public function getFormattedBalanceAttribute()
    {
        return '$' . number_format($this->balance, 2);
    }

    /**
     * Get the wallet total deposits formatted.
     */
    public function getFormattedTotalDepositsAttribute()
    {
        return '$' . number_format($this->total_deposits, 2);
    }

    /**
     * Get the wallet total withdrawals formatted.
     */
    public function getFormattedTotalWithdrawalsAttribute()
    {
        return '$' . number_format($this->total_withdrawals, 2);
    }

    /**
     * Get the wallet total earnings formatted.
     */
    public function getFormattedTotalEarningsAttribute()
    {
        return '$' . number_format($this->total_earnings, 2);
    }

    /**
     * Get the wallet total spent formatted.
     */
    public function getFormattedTotalSpentAttribute()
    {
        return '$' . number_format($this->total_spent, 2);
    }

    /**
     * Check if wallet has sufficient balance.
     */
    public function hasSufficientBalance($amount)
    {
        return $this->balance >= $amount;
    }

    /**
     * Add funds to wallet.
     */
    public function addFunds($amount, $type = 'deposit', $description = null)
    {
        $this->balance += $amount;
        $this->last_transaction_at = now();

        if ($type === 'deposit') {
            $this->total_deposits += $amount;
        } elseif ($type === 'earning') {
            $this->total_earnings += $amount;
        }

        $this->save();

        // Create transaction record
        $this->transactions()->create([
            'type' => $type,
            'amount' => $amount,
            'balance_before' => $this->balance - $amount,
            'balance_after' => $this->balance,
            'description' => $description,
        ]);
    }

    /**
     * Deduct funds from wallet.
     */
    public function deductFunds($amount, $type = 'withdrawal', $description = null)
    {
        if (!$this->hasSufficientBalance($amount)) {
            throw new \Exception('Insufficient balance');
        }

        $this->balance -= $amount;
        $this->last_transaction_at = now();

        if ($type === 'withdrawal') {
            $this->total_withdrawals += $amount;
        } elseif ($type === 'spent') {
            $this->total_spent += $amount;
        }

        $this->save();

        // Create transaction record
        $this->transactions()->create([
            'type' => $type,
            'amount' => -$amount,
            'balance_before' => $this->balance + $amount,
            'balance_after' => $this->balance,
            'description' => $description,
        ]);
    }

    /**
     * Get recent transactions.
     */
    public function getRecentTransactions($limit = 10)
    {
        return $this->transactions()->latest()->limit($limit)->get();
    }

    /**
     * Get transaction summary.
     */
    public function getTransactionSummary()
    {
        return [
            'total_transactions' => $this->transactions()->count(),
            'total_deposits' => $this->transactions()->where('type', 'deposit')->sum('amount'),
            'total_withdrawals' => abs($this->transactions()->where('type', 'withdrawal')->sum('amount')),
            'total_earnings' => $this->transactions()->where('type', 'earning')->sum('amount'),
            'total_spent' => abs($this->transactions()->where('type', 'spent')->sum('amount')),
        ];
    }
}
