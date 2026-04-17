<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EvaluationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'evaluator_id' => $this->evaluator_id,
            'volunteer_id' => $this->volunteer_id,
            'task_id' => $this->task_id,
            'rating' => $this->rating,
            'feedback' => $this->feedback,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'evaluator' => $this->whenLoaded('evaluator'),
            'volunteer' => $this->whenLoaded('volunteer'),
            'task' => $this->whenLoaded('task'),
        ];
    }
}
