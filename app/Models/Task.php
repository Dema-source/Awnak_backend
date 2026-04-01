<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasEvaluates;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;
use Spatie\Translatable\HasTranslations;


/**
 * App/Model/Task
 * presents the tasks assigned to the volunteer, which are monitored by the supervisor.
 * 
 * @property int $id
 * @property string $title
 * @property int $volunteer_id
 * @property int $opportunity_id
 * @property int $profile_id
 * @property int $hours
 * @property enum $status ("in progress", "active", "completed", "cancelled")
 * @property Carbon|null $created_at	
 * @property Carbon|null $updated_at	
 * 
 */

class Task extends Model
{
    use HasEvaluates, HasTranslations;


    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'volunteer_id',
        'opportunity_id',
        'profile_id',
        'hours',
        'status',
    ];

    /**
     * Fields to be translated.
     * @var array
     */
    public array $translatable = [
        'title',
    ];

    /**
     * Get the volunteer that owns the Task
     *
     * Relationship: Many-to-One.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function volunteer(): BelongsTo
    {
        return $this->belongsTo(Volunteer::class);
    }

    /**
     * Get the profile (supervisor) responsible for the Task
     *
     * Relationship: Many-to-One.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }

    /**
     * Get the opportinity that includes the Task
     *
     * Relationship: Many-to-One.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function opportinity(): BelongsTo
    {
        return $this->belongsTo(Opportunity::class);
    }

    /**
     * Get the volunteer_certificate associated with the Task
     *
     * Relationship: One-to-One.
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function volunteer_certificate(): HasOne
    {
        return $this->hasOne(VolunteerCertificate::class);
    }
}
