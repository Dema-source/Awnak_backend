<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VolunteerResource extends JsonResource
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
            'profile_id' => $this->profile_id,
            'status' => $this->status,
            'languages' => $this->languages,
            'availability' => $this->availability,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'user' => $this->whenLoaded('user'),
            'profile' => $this->whenLoaded('profile'),
            'applications' => $this->whenLoaded('applications'),
            'tasks' => $this->whenLoaded('tasks'),
            'evaluations' => $this->whenLoaded('evaluations'),
            'certificates' => $this->whenLoaded('certificates'),
            'badges' => $this->whenLoaded('badges'),
        ];
    }
}
