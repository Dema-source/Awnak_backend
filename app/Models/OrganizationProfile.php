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
 * @property enum $type ("Charitable organization", "Civil society organization", "Voluntary educational/university institution", "Hospital", "Religious organization", "Company with a Corporate Social Responsibility (CSR) program", "Student club/association", "Environmental organization")
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
        'type',
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
}
