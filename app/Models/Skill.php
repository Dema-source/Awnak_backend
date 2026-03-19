<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\Skill
 *
 * Represents a Skill could Volunteer has or an Opportunity required.
 *
 * @property int $id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 */

class Skill extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
    ];

    /**
     * All profiles that has the Skill
     *
     * Relationship: Many-to-Many.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function profiles(): BelongsToMany
    {
        return $this->belongsToMany(Profile::class);
    }


    /**
     * The opportunities that require the Skill
     *
     * Relationship: Many-to-Many.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function opportunities(): BelongsToMany
    {
        return $this->belongsToMany(Opportunity::class);
    }
}
