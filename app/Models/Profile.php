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
    ];

    /**
     * Fields to be translated.
     * @var array
     */
    public array $translatable = [
        'bio',
        'age',
        'gender',
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
        return $this->belongsToMany(Skill::class);
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
}
