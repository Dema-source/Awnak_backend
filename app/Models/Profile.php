<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;
use Spatie\Translatable\HasTranslations;

/**
 * App\Models\Profile
 *
 * Represents a profile belongs to a User.
 *
 * @property int $id
 * @property int $user_id
 * @property text $bio
 * @property int $age
 * @property enum $gender ("Male","Female")
 * @property json $interests
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 */
class Profile extends Model
{

    use HasTranslations;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'bio',
        'age',
        'gender',
        'interests',
    ];

    /**
     * Fields to be translated.
     * @var array
     */
    public array $translatable = [
        'bio',
        'interests',
    ];

    /**
     * Ensures automatic JSON encoding/decoding
     * @var array
     */
    protected $casts = [
        'interests' => 'array', 
    ];
    /**
     * Get the user that owns the Profile
     *
     * Relationship: One-to-One.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the volunteer owns this profile
     *
     * Relationship: One-to-One.
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function volunteer(): HasOne
    {
        return $this->hasOne(Volunteer::class);
    }

    /**
     * The Skills that belong to the Profile
     *
     * Relationship: Many-to-Many.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class,'profile_skill');
    }

    /**
     * Get all of the tasks for the Profile
     *
     * Relationship: One-to-Many.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    /**
     * Scope a query to only include profiles of a given gender.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $gender
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfGender($query, string $gender)
    {
        return $query->where('gender', $gender);
    }

    /**
     * Scope a query to only include profiles within age range.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $minAge
     * @param int $maxAge
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfAgeRange($query, int $minAge, int $maxAge)
    {
        return $query->whereBetween('age', [$minAge, $maxAge]);
    }

    /**
     * Scope a query to search profiles by bio or interests.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $searchTerm
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearchInBioOrInterests($query, string $searchTerm)
    {
        return $query->where(function ($q) use ($searchTerm) {
            $q->where('bio', 'like', "%{$searchTerm}%")
              ->orWhereJsonContains('interests', $searchTerm);
        });
    }

    /**
     * Scope a query to only include profiles with specific skills.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $skillIds
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithSkills($query, array $skillIds)
    {
        return $query->whereHas('skills', function ($q) use ($skillIds) {
            $q->whereIn('skills.id', $skillIds);
        });
    }

    /**
     * Scope a query to include profile relationships.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $relations
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithRelations($query, array $relations)
    {
        return $query->with($relations);
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
                    case 'gender':
                        $query->ofGender($value);
                        break;
                    case 'min_age':
                        if (isset($filters['max_age'])) {
                            $query->ofAgeRange($value, $filters['max_age']);
                        }
                        break;
                    case 'max_age':
                        if (isset($filters['min_age'])) {
                            $query->ofAgeRange($filters['min_age'], $value);
                        }
                        break;
                    case 'skill_ids':
                        if (is_array($value)) {
                            $query->withSkills($value);
                        }
                        break;
                    case 'search':
                        $query->searchInBioOrInterests($value);
                        break;
                    case 'created_on':
                        $query->createdOn($value);
                        break;
                    case 'created_from':
                        $query->createdFrom($value);
                        break;
                    case 'created_to':
                        $query->createdTo($value);
                        break;
                    case 'active':
                        $query->whereHas('user', function($q) use ($value) {
                            $q->where('status', $value ? 'active' : 'notActive');
                        });
                        break;
                    default:
                        $query->where($field, $value);
                        break;
                }
            }
        }

        return $query;
    }

    /**
     * Scope to filter profiles by created date.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string|null $date
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCreatedOn($query, ?string $date)
    {
        if ($date) {
            return $query->whereDate('created_at', $date);
        }
        return $query;
    }

    /**
     * Scope to filter profiles created from a specific date.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string|null $date
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCreatedFrom($query, ?string $date)
    {
        if ($date) {
            return $query->whereDate('created_at', '>=', $date);
        }
        return $query;
    }

    /**
     * Scope to filter profiles created until a specific date.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string|null $date
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCreatedTo($query, ?string $date)
    {
        if ($date) {
            return $query->whereDate('created_at', '<=', $date);
        }
        return $query;
    }

    /**
     * Scope a query to get profiles with skills count.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $count
     * @param string $operator
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithSkillsCount($query, int $count, string $operator = '>=')
    {
        return $query->withCount('skills')->having('skills_count', $operator, $count);
    }

    /**
     * Scope a query to get profiles by user ID.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int|string $userId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope a query to get profiles with bio.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithBio($query)
    {
        return $query->whereNotNull('bio')->where('bio', '!=', '');
    }

    /**
     * Scope a query to get profiles with interests.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithInterests($query)
    {
        return $query->whereNotNull('interests')->whereJsonLength('interests', '>', 0);
    }

    /**
     * Scope a query to get profiles created recently.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $days
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope a query to get profiles updated recently.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $days
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRecentlyUpdated($query, int $days = 30)
    {
        return $query->where('updated_at', '>=', now()->subDays($days));
    }
}
