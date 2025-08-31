<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Specialty extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name_en',
        'name_ar',
        'description',
        'icon',
        'color',
        'is_active',
        'sort_order',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get the doctors for the specialty.
     */
    public function doctors(): HasMany
    {
        return $this->hasMany(Doctor::class);
    }

    /**
     * Get the pricing for the specialty.
     */
    public function pricing(): HasMany
    {
        return $this->hasMany(SpecialtyPricing::class);
    }

    /**
     * Scope a query to only include active specialties.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to order by sort order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc');
    }

    /**
     * Get the specialty name based on current locale.
     */
    public function getNameAttribute()
    {
        $locale = app()->getLocale();
        return $locale === 'ar' ? $this->name_ar : $this->name_en;
    }

    /**
     * Get the specialty's total doctors count.
     */
    public function getDoctorsCountAttribute()
    {
        return $this->doctors()->active()->count();
    }

    /**
     * Get the specialty's average consultation fee.
     */
    public function getAverageConsultationFeeAttribute()
    {
        return $this->doctors()->active()->avg('consultation_fee') ?? 0;
    }
}
