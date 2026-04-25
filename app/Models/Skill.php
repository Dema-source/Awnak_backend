<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;
use Spatie\Translatable\HasTranslations;

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
    use HasTranslations;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
    ];

    /**
     * Fields to be translated.
     * @var array
     */
    public array $translatable = [
        'name'
    ];


    /**
     * All profiles that has the Skill
     *
     * Relationship: Many-to-Many.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function profiles(): BelongsToMany
    {
        return $this->belongsToMany(Profile::class, 'profile_skill');
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

    /**
     * Scope a query to search skills by name.
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
     * Scope a query to get skills with profiles count.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $minCount
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithProfilesCount($query, int $minCount = 1)
    {
        return $query->withCount('profiles')->having('profiles_count', '>=', $minCount);
    }

    /**
     * Scope a query to get skills with opportunities count.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $minCount
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithOpportunitiesCount($query, int $minCount = 1)
    {
        return $query->withCount('opportunities')->having('opportunities_count', '>=', $minCount);
    }

    /**
     * Scope a query to get skills created recently.
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
     * Scope a query to get popular skills.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePopular($query, int $limit = 10)
    {
        return $query->withCount('profiles')
            ->orderBy('profiles_count', 'desc')
            ->take($limit);
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
                    case 'min_profiles':
                        $query->withProfilesCount($value);
                        break;
                    case 'min_opportunities':
                        $query->withOpportunitiesCount($value);
                        break;
                    case 'recent_days':
                        $query->recent($value);
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

    /**
     * Scope a query to get skills not in specific profile.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int|string $profileId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNotInProfile($query, $profileId)
    {
        return $query->whereDoesntHave('profiles', function ($q) use ($profileId) {
            $q->where('profiles.id', $profileId);
        });
    }

    /**
     * Scope a query to get skills not in specific opportunity.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int|string $opportunityId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNotInOpportunity($query, $opportunityId)
    {
        return $query->whereDoesntHave('opportunities', function ($q) use ($opportunityId) {
            $q->where('opportunities.id', $opportunityId);
        });
    }
}
