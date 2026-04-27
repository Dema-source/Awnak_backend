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
            'bio' => $this->bio,
            'age' => $this->age,
            'gender' => $this->gender,   
            'interests' => $this->interests,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'user' => $this->whenLoaded('user'),
            'skills' => $this->whenLoaded('skills'),
            // 'applications' => $this->whenLoaded('applications'),
            'tasks' => $this->whenLoaded('tasks'),
            'evaluations' => $this->whenLoaded('evaluations'),
            'certificates' => $this->whenLoaded('certificates'),
            'badges' => $this->whenLoaded('badges'),
        ];
    }
}
