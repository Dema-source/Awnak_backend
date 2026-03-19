<?php

namespace App\Traits;

use Dom\Document;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Trait HasDocuments.
 *
 * This trait encapsulates the logic for the Polymorphic "Document" relationship.
 */
trait HasDocuments
{
    /**
     * Get all of the entity's documents.
     * @return MorphMany
     */
    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
}
}