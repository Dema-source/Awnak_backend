<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Spatie\Translatable\HasTranslations;

/**
 * App\Models\OrgaizationProfile
 *
 * Represents a profile belongs to an Organization.
 *
 * @property int $id
 * @property int $user_id
 * @property enum $status ("active" , "notactive")
 * @property string $license_number
 * @property enum $type ("Charitable organization", "Civil society organization", "Voluntary educational/university institution", "Hospital", "Religious organization", "Company with a Corporate Social Responsibility (CSR) program", "Student club/association", "Environmental organization")
 * @property text $bio
 * @property string $website
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 */

class OrganizationProfile extends Model
{

    use HasTranslations;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'status',
        'license_number',
        'type',
        'bio',
        'website',
        'user_id',
    ];

    /**
     * Fields to be translated.
     * @var array
     */
    public array $translatable = [
        'license_number',
        'bio',
        'website',
    ];

    /**
     * Get the user that owns this Organization Profile
     *
     * Relationship: One-to-One.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all of the opportunities for the Orgaization
     *
     * Relationship: One-to-Many.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function opportunities(): HasMany
    {
        return $this->hasMany(Opportunity::class);
    }

    /**
     * Scope to filter by active status.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to filter by inactive status.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'notactive');
    }

    /**
     * Scope to filter by status.
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
     * Scope to filter by organization type.
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
     * Scope to filter by license number.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $licenseNumber
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByLicenseNumber($query, string $licenseNumber)
    {
        return $query->where('license_number', 'like', "%{$licenseNumber}%");
    }

    /**
     * Scope to filter by website.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $website
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByWebsite($query, string $website)
    {
        return $query->where('website', 'like', "%{$website}%");
    }

    /**
     * Scope to filter by bio.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $bio
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByBio($query, string $bio)
    {
        return $query->where('bio', 'like', "%{$bio}%");
    }

    /**
     * Scope to filter by user ID.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByUserId($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to filter by active user status.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param bool $active
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByActiveUser($query, bool $active = true)
    {
        return $query->whereHas('user', function ($userQuery) use ($active) {
            $userQuery->where('status', $active ? 'active' : '!=', 'active');
        });
    }

    /**
     * Scope to filter by created date range.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string|null $fromDate
     * @param string|null $toDate
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByDateRange($query, ?string $fromDate = null, ?string $toDate = null)
    {
        if ($fromDate) {
            $query->whereDate('created_at', '>=', $fromDate);
        }
        
        if ($toDate) {
            $query->whereDate('created_at', '<=', $toDate);
        }
        
        return $query;
    }

    /**
     * Scope to filter organizations with opportunities.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithOpportunities($query)
    {
        return $query->whereHas('opportunities');
    }

    /**
     * Scope to search across multiple fields.
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
            $q->where('bio', 'like', "%{$term}%")
              ->orWhere('license_number', 'like', "%{$term}%")
              ->orWhere('website', 'like', "%{$term}%")
              ->orWhereHas('user', function ($userQuery) use ($term) {
                  $userQuery->where('name', 'like', "%{$term}%")
                           ->orWhere('email', 'like', "%{$term}%");
              });
        });
    }

    /**
     * Scope to filter by recent organizations (created within specified days).
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
     * Scope to get organizations with active users.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithActiveUser($query)
    {
        return $query->whereHas('user', function ($userQuery) {
            $userQuery->where('status', 'active');
        });
    }

    /**
     * Scope to get organizations by multiple types.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $types
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByTypes($query, array $types)
    {
        return $query->whereIn('type', $types);
    }
}
