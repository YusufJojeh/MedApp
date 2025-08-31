<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Doctor extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'specialty_id',
        'name',
        'description',
        'experience_years',
        'education',
        'languages',
        'consultation_fee',
        'rating',
        'total_reviews',
        'is_active',
        'is_verified',
        'profile_image',
        'license_number',
        'hospital_affiliation',
        'awards',
        'publications',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'experience_years' => 'integer',
        'consultation_fee' => 'decimal:2',
        'rating' => 'decimal:2',
        'total_reviews' => 'integer',
        'is_active' => 'boolean',
        'is_verified' => 'boolean',
        'awards' => 'array',
        'publications' => 'array',
    ];

    /**
     * Get the user that owns the doctor.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the specialty that owns the doctor.
     */
    public function specialty(): BelongsTo
    {
        return $this->belongsTo(Specialty::class);
    }

    /**
     * Get the appointments for the doctor.
     */
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    /**
     * Get the reviews for the doctor.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Get the working hours for the doctor.
     */
    public function workingHours(): HasMany
    {
        return $this->hasMany(WorkingHour::class);
    }

    /**
     * Get the wallet for the doctor.
     */
    public function wallet(): HasOne
    {
        return $this->hasOne(Wallet::class, 'user_id', 'user_id');
    }

    /**
     * Get the pricing overrides for the doctor.
     */
    public function pricingOverrides(): HasMany
    {
        return $this->hasMany(DoctorPricingOverride::class);
    }

    /**
     * Scope a query to only include active doctors.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include verified doctors.
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Scope a query to only include doctors by specialty.
     */
    public function scopeBySpecialty($query, $specialtyId)
    {
        return $query->where('specialty_id', $specialtyId);
    }

    /**
     * Get the doctor's average rating.
     */
    public function getAverageRatingAttribute()
    {
        return $this->reviews()->avg('rating') ?? 0;
    }

    /**
     * Get the doctor's total appointments count.
     */
    public function getTotalAppointmentsAttribute()
    {
        return $this->appointments()->count();
    }

    /**
     * Get the doctor's completed appointments count.
     */
    public function getCompletedAppointmentsAttribute()
    {
        return $this->appointments()->where('status', 'completed')->count();
    }

    /**
     * Get the doctor's upcoming appointments count.
     */
    public function getUpcomingAppointmentsAttribute()
    {
        return $this->appointments()->where('status', 'scheduled')->count();
    }

    /**
     * Get the doctor's earnings.
     */
    public function getTotalEarningsAttribute()
    {
        return $this->appointments()
            ->where('status', 'completed')
            ->sum('consultation_fee');
    }
}
