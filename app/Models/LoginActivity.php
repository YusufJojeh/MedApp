<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoginActivity extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'status',
        'ip_address',
        'user_agent',
        'location',
        'device_type',
        'browser',
        'platform',
        'failure_reason',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns the login activity.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include successful logins.
     */
    public function scopeSuccessful($query)
    {
        return $query->where('status', 'success');
    }

    /**
     * Scope a query to only include failed logins.
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope a query to only include blocked logins.
     */
    public function scopeBlocked($query)
    {
        return $query->where('status', 'blocked');
    }

    /**
     * Scope a query to only include recent activities.
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Get the device type from user agent.
     */
    public function getDeviceTypeAttribute($value)
    {
        if ($value) {
            return $value;
        }

        $userAgent = $this->user_agent;
        
        if (preg_match('/(tablet|ipad|playbook)|(android(?!.*(mobi|opera mini)))/i', strtolower($userAgent))) {
            return 'tablet';
        }
        
        if (preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|android|iemobile)/i', strtolower($userAgent))) {
            return 'mobile';
        }
        
        return 'desktop';
    }

    /**
     * Get the browser from user agent.
     */
    public function getBrowserAttribute($value)
    {
        if ($value) {
            return $value;
        }

        $userAgent = $this->user_agent;
        
        if (preg_match('/MSIE|Trident/i', $userAgent)) {
            return 'Internet Explorer';
        } elseif (preg_match('/Firefox/i', $userAgent)) {
            return 'Firefox';
        } elseif (preg_match('/Chrome/i', $userAgent)) {
            return 'Chrome';
        } elseif (preg_match('/Safari/i', $userAgent)) {
            return 'Safari';
        } elseif (preg_match('/Opera|OPR/i', $userAgent)) {
            return 'Opera';
        } elseif (preg_match('/Edge/i', $userAgent)) {
            return 'Edge';
        }
        
        return 'Unknown';
    }

    /**
     * Get the platform from user agent.
     */
    public function getPlatformAttribute($value)
    {
        if ($value) {
            return $value;
        }

        $userAgent = $this->user_agent;
        
        if (preg_match('/Windows/i', $userAgent)) {
            return 'Windows';
        } elseif (preg_match('/Mac/i', $userAgent)) {
            return 'Mac';
        } elseif (preg_match('/Linux/i', $userAgent)) {
            return 'Linux';
        } elseif (preg_match('/Android/i', $userAgent)) {
            return 'Android';
        } elseif (preg_match('/iOS/i', $userAgent)) {
            return 'iOS';
        }
        
        return 'Unknown';
    }
}
