<?php

declare(strict_types=1);

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Carbon;
use Spatie\Permission\Traits\HasPermissions;
use Spatie\Translatable\HasTranslations;


/**
 * App/Model/User
 * presents a registered user in the application.
 * 
 * @property int $id
 * @property string $name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string $phone
 * @property string $address
 * @property string|null $remember_token	
 * @property Carbon|null $created_at	
 * @property Carbon|null $updated_at	
 * 
 */
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory>
     */
    use HasRoles, HasFactory, Notifiable, HasApiTokens, HasTranslations;


    /**
     * The attributes that are mass assignable.
     *
     * Defines which fields can be passed directly to `User::create()`.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'status',
        'phone',
        'address',
    ];

    /**
     * Fields to be translated.
     * @var array
     */
    public array $translatable = [
        'name',
        'address',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the profile belongs to the User
     *
     * Relationship: One-to-One.
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class);
    }

    /**
     * Get the profile belongs to the User(Organization)
     *
     * Relationship: One-to-One.
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function organization_profile(): HasOne
    {
        return $this->hasOne(OrganizationProfile::class);
    }

    /**
     * Get all of the evaluations added by the User.
     *
     * Relationship: One-to-Many.
     * A user can add multiple evaluations for different volunteers or tasks.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function evaluations(): HasMany
    {
        return $this->hasMany(Evaluation::class);
    }

    /**
     * Scope to filter users by active status.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param bool $active
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query, bool $active = true)
    {
        return $query->where('status', $active ? 'active' : 'notActive');
    }

    /**
     * Scope to filter users by created date.
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
     * Scope to filter users created from a specific date.
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
     * Scope to filter users created until a specific date.
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
     * Scope to search users by name or email.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string|null $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, ?string $search)
    {
        if ($search) {
            return $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        return $query;
    }

    /**
     * Scope to apply multiple filters to users.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilter($query, array $filters = [])
    {
        // Apply active filter
        if (isset($filters['active'])) {
            $query->active((bool) $filters['active']);
        }

        // Apply search filter
        if (isset($filters['search'])) {
            $query->search($filters['search']);
        }

        // Apply date filters
        if (isset($filters['created_on'])) {
            $query->createdOn($filters['created_on']);
        }

        if (isset($filters['created_from'])) {
            $query->createdFrom($filters['created_from']);
        }

        if (isset($filters['created_to'])) {
            $query->createdTo($filters['created_to']);
        }

        return $query;
    }
}
