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

    /**
     * Scope a query to search countries by name.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $searchTerm
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearchByName($query, string $searchTerm)
    {
        return $query->where('name', 'like', "%{$searchTerm}%");
    }

    /**
     * Scope a query to filter by creation date.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $date
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCreatedOn($query, string $date)
    {
        return $query->whereDate('created_at', $date);
    }

    /**
     * Scope a query to filter by creation date from.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $date
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCreatedFrom($query, string $date)
    {
        return $query->whereDate('created_at', '>=', $date);
    }

    /**
     * Scope a query to filter by creation date to.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $date
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCreatedTo($query, string $date)
    {
        return $query->whereDate('created_at', '<=', $date);
    }

    /**
     * Scope a query to filter by multiple criteria.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilter($query, array $filters)
    {
        foreach ($filters as $field => $value) {
            if ($value !== null && $value !== '') {
                switch ($field) {
                    case 'search':
                        $query->searchByName($value);
                        break;
                    case 'created_at':
                        $query->createdOn($value);
                        break;
                    case 'created_from':
                        $query->createdFrom($value);
                        break;
                    case 'created_to':
                        $query->createdTo($value);
                        break;
                    default:
                        $query->where($field, $value);
                        break;
                }
            }
        }

        return $query;
    }
}
