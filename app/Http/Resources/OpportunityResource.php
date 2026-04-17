<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OpportunityResource extends JsonResource
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
            'title' => $this->title,
            'expected_duration' => $this->expected_duration,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'required_volunteers' => $this->required_volunteers,
            'status' => $this->status,
            'organization_profile_id' => $this->organization_profile_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'skills' => SkillResource::collection($this->whenLoaded('skills')),
            'organization' => $this->whenLoaded('organization'),
            'locations' => $this->whenLoaded('locations'),
        ];
    }
}
