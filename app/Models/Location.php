<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\Location
 *
 * Represents Location of the opportunity that organization sets out to provide.
 *
 * @property int $id
 * @property int|null $city_id
 * @property float|null $latitude
 * @property float|null $longitude
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property-read City|null $city
 * @property-read Country|null $country (through city)
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Opportunity> $opportunities
 */
class Location extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'city_id',
        'latitude',
        'longitude',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'city_id' => 'integer',
    ];

    /**
     * Get the city that owns the location.
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    /**
     * Get the country through the city relationship.
     */
    public function country()
    {
        return $this->hasOneThrough(Country::class, City::class);
    }

    /**
     * Get the opportunities for the location.
     */
    public function opportunities(): BelongsToMany
    {
        return $this->belongsToMany(Opportunity::class, 'location_opportunity')
            ->withPivot([
                'building_name',
                'floor_number',
                'apartment_number',
                'landmark',
            ])
            ->withTimestamps();
    }

    /**
     * The locations for the Opportunity
     *
     * Relationship: One-to-Many.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function opportunityLocations(): HasMany
    {
        return $this->hasMany(LocationOpportunity::class);
    }

    /**
     * Scope to filter by city.
     */
    public function scopeByCity($query, $cityId)
    {
        return $query->where('city_id', $cityId);
    }

    /**
     * Scope to filter by country (through city).
     */
    public function scopeByCountry($query, $countryId)
    {
        return $query->whereHas('city', function ($cityQuery) use ($countryId) {
            $cityQuery->where('country_id', $countryId);
        });
    }

    /**
     * Scope to filter by coordinates within a certain radius.
     */
    public function scopeWithinRadius($query, $latitude, $longitude, $radiusKm = 10)
    {
        $haversine = "(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude))))";
        
        return $query->selectRaw("*, {$haversine} AS distance")
            ->havingRaw("distance <= ?", [$radiusKm])
            ->orderBy('distance')
            ->setBindings([$latitude, $longitude, $latitude], 'select');
    }

    /**
     * Scope to load city and country relationships.
     */
    public function scopeWithCityAndCountry($query)
    {
        return $query->with(['city.country']);
    }

    /**
     * Scope to load opportunities relationship.
     */
    public function scopeWithOpportunities($query)
    {
        return $query->with(['opportunities']);
    }

    /**
     * Scope to filter by opportunity.
     */
    public function scopeByOpportunity($query, $opportunityId)
    {
        return $query->whereHas('opportunities', function ($opportunityQuery) use ($opportunityId) {
            $opportunityQuery->where('opportunities.id', $opportunityId);
        });
    }

    /**
     * Get full address including city and country.
     */
    public function getFullAddressAttribute(): string
    {
        $address = '';
        
        if ($this->city) {
            $address .= $this->city->name;
            if ($this->city->country) {
                $address .= ', ' . $this->city->country->name;
            }
        }
        
        return $address;
    }

    /**
     * Get coordinates as array.
     */
    public function getCoordinatesAttribute(): array
    {
        return [
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
        ];
    }
}
