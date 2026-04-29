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
 * @property json $languages
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
     * Ensures automatic JSON encoding/decoding
     * @var array
     */
    protected $casts = [
        'languages' => 'array',
        'availability' => 'array',
    ];

    /**
     * Fields to be translated.
     * @var array
     */
    public array $translatable = [
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

    /**
     * Scope a query to search volunteers by multiple fields.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string|null $term
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, ?string $term = null)
    {
        if (!$term) {
            return $query;
        }

        return $query->where(function ($q) use ($term) {
            $q->whereHas('profile', function ($profileQuery) use ($term) {
                $profileQuery->where('bio', 'like', "%{$term}%")
                    ->orWhereJsonContains('interests', $term);
            })
            ->orWhere('languages', 'like', '%"' . $term . '"%')
            ->orWhere('experience_years', 'like', "%{$term}%");
        });
    }

    /**
     * Scope a query to filter by volunteer status.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $status
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to filter by experience years.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $experienceYears
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByExperience($query, string $experienceYears)
    {
        return $query->where('experience_years', $experienceYears);
    }

    /**
     * Scope a query to filter by languages.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array|string $languages
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByLanguages($query, array|string $languages)
    {
        // Convert string to array if needed
        $languageArray = is_array($languages) ? $languages : [$languages];
        
        return $query->where(function ($q) use ($languageArray) {
            foreach ($languageArray as $language) {
                $q->orWhereJsonContains('languages', $language);
            }
        });
    }

    /**
     * Scope a query to filter by availability.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array|string $availability
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByAvailability($query, array|string $availability)
    {
        // Convert string to array if needed
        $availabilityArray = is_array($availability) ? $availability : [$availability];
        
        return $query->where(function ($q) use ($availabilityArray) {
            foreach ($availabilityArray as $time) {
                $q->orWhereJsonContains('availability', $time);
            }
        });
    }

    /**
     * Scope a query to filter by profile ID.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $profileId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByProfileId($query, int $profileId)
    {
        return $query->where('profile_id', $profileId);
    }

    /**
     * Scope a query to filter by active user status.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param bool $active
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByActiveUser($query, bool $active = true)
    {
        return $query->whereHas('profile.user', function ($userQuery) use ($active) {
            $userQuery->where('status', $active ? 'active' : 'notActive');
        });
    }

    /**
     * Scope to filter volunteers created on a specific date.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string|null $date
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCreatedOn($query, ?string $date)
    {
        if ($date) {
            return $query->where('created_at', $date);
        }
        return $query;
    }

    /**
     * Scope to filter volunteers created from a specific date.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string|null $date
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCreatedFrom($query, ?string $date)
    {
        if ($date) {
            return $query->where('created_at', '>=', $date);
        }
        return $query;
    }

    /**
     * Scope to filter volunteers created until a specific date.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string|null $date
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCreatedTo($query, ?string $date)
    {
        if ($date) {
            return $query->where('created_at', '<=', $date);
        }
        return $query;
    }

    /**
     * Scope to filter volunteers by date range.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string|null $fromDate
     * @param string|null $toDate
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByDateRange($query, ?string $fromDate = null, ?string $toDate = null)
    {
        if ($fromDate) {
            $query->where('created_at', '>=', $fromDate);
        }
        
        if ($toDate) {
            $query->where('created_at', '<=', $toDate);
        }
        
        return $query;
    }

    /**
     * Scope to filter volunteers with active status.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to filter volunteers with inactive status.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'In_active');
    }

    /**
     * Scope to filter volunteers with pending status.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to filter volunteers with blocked status.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeBlocked($query)
    {
        return $query->where('status', 'blocked');
    }
}
