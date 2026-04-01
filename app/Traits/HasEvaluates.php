<?php

namespace App\Traits;

use App\Models\Evaluation;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Trait HasEvaluates.
 *
 * This trait encapsulates the logic for the Polymorphic "Evaluate" relationship.
 */
trait HasEvaluates
{
    /**
     * Get all of the entity's evaluates.
     * @return MorphMany
     */
    public function evaluation(): MorphMany
    {
        return $this->morphMany(Evaluation::class, 'evaluable');
    }
}
