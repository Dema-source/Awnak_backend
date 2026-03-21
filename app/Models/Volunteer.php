<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasDocuments;
use App\Traits\HasEvaluates;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Spatie\Translatable\HasTranslations;

/**
 * App\Models\Skill
 *
 * Represents a Volunteer in system.
 *
 * @property int $id
 * @property int $profile_id
 * @property enum $experience_years ("one year", "two years", "three years", "four years", "five years", "More than five years")
 * @property enum $status ("active", "In_active", "pending", "blocked")
 * @property json $availability
 * @property string $languages
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 */

class Volunteer extends Model
{
    use HasEvaluates, HasDocuments, HasTranslations;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'profile_id',
        'experience_years',
        'status',
        'availability',
        'languages',
    ];

    /**
     * Fields to be translated.
     * @var array
     */
    public array $translatable = [
        'experience_years',
        'status',
        'availability',
        'languages',
    ];

    /**
     * Get the profile for this volunteer
     *
     * Relationship: One-to-One.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }

    /**
     * Get all of the opportunity applications for the Volunteer
     *
     * Relationship: One-to-Many. 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    /**
     * Get all of the tasks for the Volunteer
     *
     * Relationship: One-to-Many.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    /**
     * The certificates that volunteer get
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function certificates(): HasMany
    {
        return $this->hasMany(VolunteerCertificate::class);
    }

    /**
     * The badges that volunteer gets
     *
     * Relationship: Many-to-Many.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function badges(): BelongsToMany
    {
        return $this->belongsToMany(Badge::class);
    }
}
