<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * App/Model/Certificate
 * presents electronic certificates for volunteers after completing specific tasks or 
 * achieving a certain number of volunteer hours.
 * 
 * @property int $id
 * @property enum $type ("internal", "external")
 * @property string $value
 * @property Carbon|null $created_at	
 * @property Carbon|null $updated_at	
 * 
 */

class Certificate extends Model
{
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
     * The volunteers that get the Certificate
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function volunteers(): HasMany
    {
        return $this->hasMany(VolunteerCertificate::class);
    }
}
