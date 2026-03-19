<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App/Model/VolunteerCertificate
 * presents a recognition for volunteers after they 
 * have obtained a certain number of certificates.
 * 
 * @property int $id
 * @property int $volunteer_id
 * @property int $certificate_id
 * @property int $task_id
 * @property Carbon|null $created_at	
 * @property Carbon|null $updated_at	
 * 
 */

class VolunteerCertificate extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'volunteer_id',
        'certificate_id',
        'task_id',
    ];

    /**
     * Get the task that associated with the VolunteerCertificate
     *
     * Relationship: One-to-One.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }
    /**
     * Get the volunteer that associated with the VolunteerCertificate
     *
     * Relationship: One-to-One.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function volunteer(): BelongsTo
    {
        return $this->belongsTo(Volunteer::class);
    }
    /**
     * Get the certificate that associated with the VolunteerCertificate
     *
     * Relationship: One-to-One.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function certificate(): BelongsTo
    {
        return $this->belongsTo(Certificate::class);
    }
}
