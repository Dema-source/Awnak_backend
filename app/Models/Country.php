<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Country extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'dialing_code',
        'currency',
        'capital',
        'region',
        'subregion',
        'latitude',
        'longitude',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    /**
     * Get the cities for this country.
     */
    public function cities(): HasMany
    {
        return $this->hasMany(City::class);
    }

    /**
     * Get the locations for this country through cities.
     */
    public function locations(): \Illuminate\Database\Eloquent\Relations\HasManyThrough
    {
        return $this->hasManyThrough(Location::class, City::class);
    }

    /**
     * Scope to get only active countries.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
