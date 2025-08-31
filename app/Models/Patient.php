<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Patient extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'medical_history',
        'allergies',
        'current_medications',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relationship',
        'insurance_provider',
        'insurance_number',
        'blood_type',
        'height',
        'weight',
        'is_active',
        'profile_completion_percentage',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'medical_history' => 'array',
        'allergies' => 'array',
        'current_medications' => 'array',
        'height' => 'decimal:2',
        'weight' => 'decimal:2',
        'is_active' => 'boolean',
        'profile_completion_percentage' => 'integer',
    ];

    /**
     * Get the user that owns the patient.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the appointments for the patient.
     */
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    /**
     * Get the reviews by the patient.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Get the payments by the patient.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get the wallet for the patient.
     */
    public function wallet(): HasOne
    {
        return $this->hasOne(Wallet::class, 'user_id', 'user_id');
    }

    /**
     * Get the AI conversations for the patient.
     */
    public function aiConversations(): HasMany
    {
        return $this->hasMany(AiConversation::class, 'user_id', 'user_id');
    }

    /**
     * Scope a query to only include active patients.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the patient's total appointments count.
     */
    public function getTotalAppointmentsAttribute()
    {
        return $this->appointments()->count();
    }

    /**
     * Get the patient's completed appointments count.
     */
    public function getCompletedAppointmentsAttribute()
    {
        return $this->appointments()->where('status', 'completed')->count();
    }

    /**
     * Get the patient's upcoming appointments count.
     */
    public function getUpcomingAppointmentsAttribute()
    {
        return $this->appointments()->where('status', 'scheduled')->count();
    }

    /**
     * Get the patient's total spent.
     */
    public function getTotalSpentAttribute()
    {
        return $this->payments()->where('status', 'completed')->sum('amount');
    }

    /**
     * Calculate BMI.
     */
    public function getBmiAttribute()
    {
        if ($this->height && $this->weight) {
            $heightInMeters = $this->height / 100;
            return round($this->weight / ($heightInMeters * $heightInMeters), 2);
        }
        return null;
    }

    /**
     * Get BMI category.
     */
    public function getBmiCategoryAttribute()
    {
        $bmi = $this->bmi;
        if (!$bmi) return null;

        if ($bmi < 18.5) return 'Underweight';
        if ($bmi < 25) return 'Normal weight';
        if ($bmi < 30) return 'Overweight';
        return 'Obese';
    }

    /**
     * Calculate profile completion percentage.
     */
    public function calculateProfileCompletion()
    {
        $fields = [
            'medical_history',
            'allergies',
            'current_medications',
            'emergency_contact_name',
            'emergency_contact_phone',
            'insurance_provider',
            'blood_type',
            'height',
            'weight'
        ];

        $completed = 0;
        foreach ($fields as $field) {
            if (!empty($this->$field)) {
                $completed++;
            }
        }

        return round(($completed / count($fields)) * 100);
    }
}
