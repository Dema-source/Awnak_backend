<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Spatie\Translatable\HasTranslations;

/**
 * App\Models\Application
 *
 * Represents an application submitted by a volunteer to volunteer 
 * in a volunteering opportunity offered by an organization.
 *
 * @property int $id
 * @property int $volunteer_id
 * @property int $opportunity_id
 * @property enum $status ("accepted", "rejected", "pending")
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 */

class Application extends Model
{

    use HasTranslations;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'volunteer_id',
        'opportunity_id',
        'status',
    ];

    /**
     * Get the volunteer that add the Application
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function volunteer(): BelongsTo
    {
        return $this->belongsTo(Volunteer::class);
    }

    /**
     * Get the opportunity that belong to the Application
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function opportunity(): BelongsTo
    {
        return $this->belongsTo(Opportunity::class);
    }
}
