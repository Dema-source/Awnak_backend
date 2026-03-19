<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;

/**
 * App/Model/Evaluation
 * Represents a piece of feedback left by a User.
 * This model is "Polymorphic", meaning it can belong to different parent entities
 * (like an Volunteer or a Task) using the same database table.
 * 
 * @property int $id
 * @property int $user_id
 * @property enum $rating (1, 2, 3, 4, 5)
 * @property text $comment
 * @property Carbon|null $created_at	
 * @property Carbon|null $updated_at	
 * 
 */

class Evaluation extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'rating',
        'comment',
    ];

    /**
     * Get the parent evaluable model (polymorphic).
     *
     * This method looks at the `evaluable_type` column (e.g., 'task','volunteer')
     * and the `evaluable_id` column (e.g., 1) to dynamically determine
     * which model to evaluate.
     *
     * If `evaluable_type` is 'task', it returns an instance of App\Models\Task.
     * If `evaluable_type` is 'volunteer', it returns an instance of App\Models\Volunteer.
     *
     * @return MorphTo
     */
    public function evaluable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the user that give the evaluation.
     *
     * Relationship: Many-to-One.
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
