<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
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
        'appointment_id',
        'rating',
        'comment',
        'is_verified',
        'is_approved',
        'approved_at',
        'approved_by',
        'helpful_votes',
        'unhelpful_votes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'rating' => 'integer',
        'is_verified' => 'boolean',
        'is_approved' => 'boolean',
        'approved_at' => 'datetime',
        'helpful_votes' => 'integer',
        'unhelpful_votes' => 'integer',
    ];

    /**
     * Get the patient that owns the review.
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Get the doctor that owns the review.
     */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    /**
     * Get the appointment that owns the review.
     */
    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    /**
     * Scope a query to only include approved reviews.
     */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    /**
     * Scope a query to only include verified reviews.
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Scope a query to only include reviews by rating.
     */
    public function scopeByRating($query, $rating)
    {
        return $query->where('rating', $rating);
    }

    /**
     * Scope a query to only include high-rated reviews (4-5 stars).
     */
    public function scopeHighRated($query)
    {
        return $query->where('rating', '>=', 4);
    }

    /**
     * Get the review rating stars.
     */
    public function getRatingStarsAttribute()
    {
        $stars = '';
        for ($i = 1; $i <= 5; $i++) {
            if ($i <= $this->rating) {
                $stars .= '<i class="fas fa-star text-gold"></i>';
            } else {
                $stars .= '<i class="fas fa-star text-gray-300"></i>';
            }
        }
        return $stars;
    }

    /**
     * Get the review rating text.
     */
    public function getRatingTextAttribute()
    {
        return match($this->rating) {
            1 => 'Poor',
            2 => 'Fair',
            3 => 'Good',
            4 => 'Very Good',
            5 => 'Excellent',
            default => 'Not Rated',
        };
    }

    /**
     * Get the review status badge class.
     */
    public function getStatusBadgeClassAttribute()
    {
        if (!$this->is_approved) {
            return 'bg-yellow-100 text-yellow-800';
        }

        return $this->is_verified ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800';
    }

    /**
     * Get the review status text.
     */
    public function getStatusTextAttribute()
    {
        if (!$this->is_approved) {
            return 'Pending';
        }

        return $this->is_verified ? 'Verified' : 'Approved';
    }

    /**
     * Get the total votes.
     */
    public function getTotalVotesAttribute()
    {
        return $this->helpful_votes + $this->unhelpful_votes;
    }

    /**
     * Get the helpful percentage.
     */
    public function getHelpfulPercentageAttribute()
    {
        if ($this->total_votes === 0) {
            return 0;
        }

        return round(($this->helpful_votes / $this->total_votes) * 100);
    }

    /**
     * Check if review is helpful.
     */
    public function getIsHelpfulAttribute()
    {
        return $this->helpful_percentage >= 50;
    }

    /**
     * Get the review date formatted.
     */
    public function getFormattedDateAttribute()
    {
        return $this->created_at->format('M d, Y');
    }

    /**
     * Get the review time ago.
     */
    public function getTimeAgoAttribute()
    {
        return $this->created_at->diffForHumans();
    }
}
