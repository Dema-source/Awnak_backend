<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Spatie\Translatable\HasTranslations;


/**
 * App/Model/TaskHour
 * presents the hours of the task.
 * 
 * @property int $id
 * @property int $task_id
 * @property int $hours
 * @property Carbon|null $created_at	
 * @property Carbon|null $updated_at	
 * 
 */

class TaskHour extends Model
{

    use HasTranslations;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'task_id',
        'hours',
    ];

    /**
     * Fields to be translated.
     * @var array
     */
    public array $translatable = [
        'hours',
    ];

    /**
     * Get the task that has the TaskHour
     *
     * Relationship: One-to-One. 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }
}
