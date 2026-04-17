<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
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
            'opportunity_id' => $this->opportunity_id,
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status,
            'deadline' => $this->deadline,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'opportunity' => $this->whenLoaded('opportunity'),
            'volunteers' => $this->whenLoaded('volunteers'),
            'evaluations' => $this->whenLoaded('evaluations'),
            'documents' => $this->whenLoaded('documents'),
        ];
    }
}
