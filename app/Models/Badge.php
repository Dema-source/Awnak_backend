<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;
use Spatie\Translatable\HasTranslations;


/**
 * App/Model/Badge
 * presents a recognition for volunteers after they 
 * have obtained a certain number of certificates.
 * 
 * @property int $id
 * @property enum $type ("bronze", "silver", "gold")
 * @property string $value	
 * @property Carbon|null $created_at	
 * @property Carbon|null $updated_at	
 * 
 */

class Badge extends Model
{

    use HasTranslations;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'type',
        'value',
    ];

    /**
     * Fields to be translated.
     * @var array
     */
    public array $translatable = [
        'type',
        'value',
    ];

    /**
     * The volunteers who get the badge
     *
     * Relationship: Many-to-Many.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function volunteers(): BelongsToMany
    {
        return $this->belongsToMany(Volunteer::class);
    }
}
