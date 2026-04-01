<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Spatie\Translatable\HasTranslations;

/**
 * App/Model/Certificate
 * presents electronic certificates for volunteers after completing specific tasks or 
 * achieving a certain number of volunteer hours.
 * 
 * @property int $id
 * @property string $type 
 * @property string $description
 * @property Carbon|null $created_at	
 * @property Carbon|null $updated_at	
 * 
 */

class Certificate extends Model
{
    use HasTranslations;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'type',
        'description',
    ];

    /**
     * Fields to be translated.
     * @var array
     */
    public array $translatable = [
        'type',
        'description',
    ];

    /**
     * The volunteers that get the Certificate
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function volunteers(): HasMany
    {
        return $this->hasMany(VolunteerCertificate::class);
    }
}
