<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
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
            'user_id' => $this->user_id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'phone' => $this->phone,
            'date_of_birth' => $this->date_of_birth,
            'address' => $this->address,
            'bio' => $this->bio,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'user' => $this->whenLoaded('user'),
            'skills' => $this->whenLoaded('skills'),
            'applications' => $this->whenLoaded('applications'),
            'tasks' => $this->whenLoaded('tasks'),
            'evaluations' => $this->whenLoaded('evaluations'),
            'certificates' => $this->whenLoaded('certificates'),
            'badges' => $this->whenLoaded('badges'),
        ];
    }
}
