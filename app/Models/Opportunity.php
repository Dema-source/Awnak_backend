<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasDocuments;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;
use Spatie\Translatable\HasTranslations;

/**
 * App\Models\Opportunity
 *
 * Represents Volunteer opportunities offered by organizations.
 *
 * @property int $id
 * @property string $title
 * @property int $organization_profile_id
 * @property string $expected_duration
 * @property date $start_date
 * @property date $end_date
 * @property int $required_volunteer
 * @property enum $status ('open', 'closed', 'filled', 'cancelled')
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 */

class Opportunity extends Model
{

    use HasDocuments, HasTranslations;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'organization_profile_id',
        'expected_duration',
        'start_date',
        'end_date',
        'required_volunteers',
        'status',
    ];

    /**
     * Fields to be translated.
     * @var array
     */
    public array $translatable = [
        'title',
        'expected_duration',
        'required_volunteer',
    ];

    /**
     * Get the organization that sets out the Opportunity
     *
     * Relationship: Many-to-One.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(OrganizationProfile::class);
    }

    /**
     * Get the location that the Opportunity available in
     *
     * Relationship: One-to-One.
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function location(): HasOne
    {
        return $this->hasOne(Location::class);
    }

    /**
     * The required skills for the Opportunity 
     *
     * Relationship: Many-to-Many.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class);
    }

    /**
     * Get all of the applications(volunteers applications) related to the Opportunity
     *
     * Relationship: One-to-Many.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    /**
     * Get all of the tasks for the Opportunity
     *
     * Relationship: One-to-Many.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }
}
