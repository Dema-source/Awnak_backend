<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Spatie\Translatable\HasTranslations;

/**
 * App\Models\Location
 *
 * Represents Location of the opportunity that the organization sets out to provide.
 *
 * @property int $id
 * @property decimal $latitude
 * @property decimal $longtude
 * @property string $address
 * @property string $city
 * @property string $country
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 */

class Location extends Model
{
    use HasTranslations;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'opportunity_id',
        'latitude',
        'longtude',
        'address',
        'city',
        'country',
    ];

    /**
     * Fields to be translated.
     * @var array
     */
    public array $translatable = [
        'address',
        'city',
        'country',
    ];

    /**
     * Get the opportunity located in a specifice location
     *
     * Relationship: One-to-One.
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function opportunity(): BelongsTo
    {
        return $this->belongsTo(Opportunity::class);
    }
}
