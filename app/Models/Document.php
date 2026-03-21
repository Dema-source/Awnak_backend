<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;
use Spatie\Translatable\HasTranslations;

/**
 * App/Model/Document
 * Represents a document added by User - Volunteer or Organization - 
 * (like an Volunteer or a Task) using the same database table.
 * 
 * @property int $id
 * @property string $title
 * @property string $path
 * @property string $type
 * @property Carbon|null $created_at	
 * @property Carbon|null $updated_at	
 * 
 */

class Document extends Model
{

    use HasTranslations;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'path',
        'type',
    ];

    /**
     * Fields to be translated.
     * @var array
     */
    public array $translatable = [
        'title',
        'path',
        'type',
    ];

    /**
     * Get the parent documentable model (polymorphic).
     *
     * This method looks at the `documentable_type` column (e.g., 'opportunity','volunteer')
     * and the `evaluable_id` column (e.g., 1) to dynamically determine
     * which model to document.
     *
     * If `evaluable_type` is 'opportunity', it returns an instance of App\Models\Opportunity.
     * If `evaluable_type` is 'volunteer', it returns an instance of App\Models\Volunteer.
     *
     * @return MorphTo
     */
    public function documentable(): MorphTo
    {
        return $this->morphTo();
    }
}
