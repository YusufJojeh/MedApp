<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Notification extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'icon',
        'color',
        'data',
        'read_at',
        'sent_at',
        'priority',
        'channel',
        'is_sent',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
        'sent_at' => 'datetime',
        'is_sent' => 'boolean',
    ];

    /**
     * Get the user that owns the notification.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include unread notifications.
     */
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    /**
     * Scope a query to only include read notifications.
     */
    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }

    /**
     * Scope a query to only include notifications by type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope a query to only include notifications by priority.
     */
    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope a query to only include recent notifications.
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', Carbon::now()->subDays($days));
    }

    /**
     * Check if notification is unread.
     */
    public function getIsUnreadAttribute()
    {
        return is_null($this->read_at);
    }

    /**
     * Check if notification is read.
     */
    public function getIsReadAttribute()
    {
        return !is_null($this->read_at);
    }

    /**
     * Get the notification time ago.
     */
    public function getTimeAgoAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Get the notification color class.
     */
    public function getColorClassAttribute()
    {
        return match($this->color) {
            'blue' => 'bg-blue-100 text-blue-800 border-blue-200',
            'green' => 'bg-green-100 text-green-800 border-green-200',
            'red' => 'bg-red-100 text-red-800 border-red-200',
            'yellow' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
            'gray' => 'bg-gray-100 text-gray-800 border-gray-200',
            default => 'bg-blue-100 text-blue-800 border-blue-200',
        };
    }

    /**
     * Get the notification priority class.
     */
    public function getPriorityClassAttribute()
    {
        return match($this->priority) {
            'low' => 'border-l-4 border-gray-400',
            'normal' => 'border-l-4 border-blue-400',
            'high' => 'border-l-4 border-yellow-400',
            'urgent' => 'border-l-4 border-red-400',
            default => 'border-l-4 border-blue-400',
        };
    }

    /**
     * Mark notification as read.
     */
    public function markAsRead()
    {
        $this->update(['read_at' => Carbon::now()]);
    }

    /**
     * Mark notification as unread.
     */
    public function markAsUnread()
    {
        $this->update(['read_at' => null]);
    }

    /**
     * Mark notification as sent.
     */
    public function markAsSent()
    {
        $this->update([
            'is_sent' => true,
            'sent_at' => Carbon::now()
        ]);
    }
}
