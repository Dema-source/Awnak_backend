<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\OpportunityLocation
 *
 * Represents the relationship between Opportunity and Location with additional pivot data.
 *
 * @property int $id
 * @property int $opportunity_id
 * @property int $location_id
 * @property string|null $building_name
 * @property string|null $floor_number
 * @property string|null $apartment_number
 * @property string|null $landmark
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property-read Opportunity $opportunity
 * @property-read Location $location
 */
class LocationOpportunity extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'opportunity_id',
        'location_id',
        'building_name',
        'floor_number',
        'apartment_number',
        'landmark',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'opportunity_id' => 'integer',
        'location_id' => 'integer',
        'building_name' => 'string',
        'floor_number' => 'integer',
        'apartment_number' => 'integer',
        'landmark' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the opportunity that owns this pivot record.
     */
    public function opportunity(): BelongsTo
    {
        return $this->belongsTo(Opportunity::class);
    }

    /**
     * Get the location that owns this pivot record.
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }
}
