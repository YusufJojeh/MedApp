<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiConversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'user_message',
        'ai_response',
        'intent',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    /**
     * Get the user that owns the conversation.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include conversations for a specific user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope a query to only include conversations with a specific intent.
     */
    public function scopeWithIntent($query, $intent)
    {
        return $query->where('intent', $intent);
    }

    /**
     * Scope a query to only include recent conversations.
     */
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Get the conversation summary.
     */
    public function getSummaryAttribute()
    {
        return [
            'total_conversations' => $this->count(),
            'intents' => $this->select('intent')
                ->distinct()
                ->pluck('intent')
                ->filter()
                ->values(),
            'recent_activity' => $this->recent()->count(),
        ];
    }
}
