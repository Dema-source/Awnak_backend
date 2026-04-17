<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LocationResource extends JsonResource
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
            'city_id' => $this->city_id,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'city' => $this->whenLoaded('city'),
            'country' => $this->whenLoaded('country'),
            'opportunities' => $this->whenLoaded('opportunities'),
            'opportunity_count' => $this->whenCounted('opportunities'),
            'coordinates' => $this->coordinates,
        ];
    }
}
