<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApplicationResource extends JsonResource
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
            'volunteer_id' => $this->volunteer_id,
            'status' => $this->status,
            'applied_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'opportunity' => $this->whenLoaded('opportunity'),
            'volunteer' => $this->whenLoaded('volunteer'),
        ];
    }
}
