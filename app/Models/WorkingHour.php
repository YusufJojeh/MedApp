<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkingHour extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'doctor_id',
        'day_of_week',
        'start_time',
        'end_time',
        'is_available',
        'break_start_time',
        'break_end_time',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'day_of_week' => 'integer',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'is_available' => 'boolean',
        'break_start_time' => 'datetime',
        'break_end_time' => 'datetime',
    ];

    /**
     * Get the doctor that owns the working hour.
     */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    /**
     * Get the day name.
     */
    public function getDayNameAttribute()
    {
        $days = [
            0 => 'Monday',
            1 => 'Tuesday',
            2 => 'Wednesday',
            3 => 'Thursday',
            4 => 'Friday',
            5 => 'Saturday',
            6 => 'Sunday',
        ];

        return $days[$this->day_of_week] ?? 'Unknown';
    }

    /**
     * Get the short day name.
     */
    public function getShortDayNameAttribute()
    {
        $days = [
            0 => 'Mon',
            1 => 'Tue',
            2 => 'Wed',
            3 => 'Thu',
            4 => 'Fri',
            5 => 'Sat',
            6 => 'Sun',
        ];

        return $days[$this->day_of_week] ?? 'Unknown';
    }

    /**
     * Get the working hours formatted.
     */
    public function getWorkingHoursFormattedAttribute()
    {
        if (!$this->is_available) {
            return 'Not Available';
        }

        $startTime = $this->start_time ? $this->start_time->format('H:i') : '';
        $endTime = $this->end_time ? $this->end_time->format('H:i') : '';

        return $startTime && $endTime ? "{$startTime} - {$endTime}" : 'Not Set';
    }

    /**
     * Get the break time formatted.
     */
    public function getBreakTimeFormattedAttribute()
    {
        if (!$this->break_start_time || !$this->break_end_time) {
            return 'No Break';
        }

        $breakStart = $this->break_start_time->format('H:i');
        $breakEnd = $this->break_end_time->format('H:i');

        return "{$breakStart} - {$breakEnd}";
    }

    /**
     * Check if working hour is today.
     */
    public function getIsTodayAttribute()
    {
        return $this->day_of_week === now()->dayOfWeek - 1;
    }

    /**
     * Check if currently working.
     */
    public function getIsCurrentlyWorkingAttribute()
    {
        if (!$this->is_available || !$this->start_time || !$this->end_time) {
            return false;
        }

        $now = now();
        $startTime = $this->start_time;
        $endTime = $this->end_time;

        // Convert to today's date for comparison
        $startTimeToday = $now->copy()->setTime($startTime->hour, $startTime->minute);
        $endTimeToday = $now->copy()->setTime($endTime->hour, $endTime->minute);

        return $now->between($startTimeToday, $endTimeToday);
    }

    /**
     * Scope a query to only include available working hours.
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    /**
     * Scope a query to only include working hours for a specific day.
     */
    public function scopeForDay($query, $dayOfWeek)
    {
        return $query->where('day_of_week', $dayOfWeek);
    }

    /**
     * Scope a query to only include today's working hours.
     */
    public function scopeToday($query)
    {
        return $query->where('day_of_week', now()->dayOfWeek - 1);
    }
}
