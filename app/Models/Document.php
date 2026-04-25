<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;
use Spatie\Translatable\HasTranslations;

/**
 * App/Model/Document
 * Represents a document added by User - Volunteer or Organization - 
 * (like an Volunteer or a Task) using the same database table.
 * 
 * @property int $id
 * @property string $title
 * @property string $path
 * @property string $type
 * @property Carbon|null $created_at	
 * @property Carbon|null $updated_at	
 * 
 */

class Document extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'path',
        'type',
        'documentable_type',
        'documentable_id',
    ];

    /**
     * Get the parent documentable model (polymorphic).
     *
     * This method looks at the `documentable_type` column (e.g., 'opportunity','volunteer')
     * and the `evaluable_id` column (e.g., 1) to dynamically determine
     * which model to document.
     *
     * If `evaluable_type` is 'opportunity', it returns an instance of App\Models\Opportunity.
     * If `evaluable_type` is 'volunteer', it returns an instance of App\Models\Volunteer.
     *
     * @return MorphTo
     */
    public function documentable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope to search documents by title.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $searchTerm
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearchByTitle($query, string $searchTerm)
    {
        return $query->where('title', 'like', "%{$searchTerm}%");
    }

    /**
     * Scope to filter documents by type.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope to filter documents by user ID (through Volunteer model).
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByVolunteer($query, int $userId)
    {
        return $query->where('documentable_type', 'App\Models\Volunteer')
            ->whereHas('documentable', function ($q) use ($userId) {
                $q->whereHas('profile', function ($profileQ) use ($userId) {
                    $profileQ->where('user_id', $userId);
                });
            });
    }

    /**
     * Scope to filter documents by organization ID.
     * Handles both Opportunity and OrganizationProfile documentable types.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $organizationId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByOrganization($query, int $organizationId)
    {
        return $query->where(function ($q) use ($organizationId) {
            // Documents attached to Opportunity model (which has organization_profile_id)
            $q->where('documentable_type', 'App\Models\Opportunity')
                ->whereHas('documentable', function ($subQ) use ($organizationId) {
                    $subQ->where('organization_profile_id', $organizationId);
                });
        });
    }

    /**
     * Scope to filter documents created on a specific date.
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
     * Scope to filter documents created from a specific date.
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
     * Scope to filter documents created to a specific date.
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
     * Scope to apply multiple filters to documents.
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
                        $query->searchByTitle($value);
                        break;
                    case 'type':
                        $query->byType($value);
                        break;
                    case 'user_id':
                        $query->byUser($value);
                        break;
                    case 'organization_id':
                        $query->byOrganization($value);
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
                    default:
                        $query->where($field, $value);
                        break;
                }
            }
        }

        return $query;
    }
}
