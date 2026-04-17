<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CityResource extends JsonResource
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
            'country_id' => $this->country_id,
            'name' => $this->name,
            'state' => $this->state,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'postal_code' => $this->postal_code,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'country' => $this->whenLoaded('country'),
            'locations_count' => $this->whenCounted('locations'),
            'locations' => $this->whenLoaded('locations'),
        ];
    }
}
