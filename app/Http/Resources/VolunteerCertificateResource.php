<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VolunteerCertificateResource extends JsonResource
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
            'volunteer_id' => $this->volunteer_id,
            'task_id' => $this->task_id,
            'certificate_id' => $this->certificate_id,
            'issued_date' => $this->issued_date,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'volunteer' => $this->whenLoaded('volunteer'),
            'task' => $this->whenLoaded('task'),
            'certificate' => $this->whenLoaded('certificate'),
        ];
    }
}
