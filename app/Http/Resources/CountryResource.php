<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CountryResource extends JsonResource
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
            'name' => $this->name,
            'code' => $this->code,
            'dialing_code' => $this->dialing_code,
            'currency' => $this->currency,
            'capital' => $this->capital,
            'region' => $this->region,
            'subregion' => $this->subregion,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'cities_count' => $this->whenCounted('cities'),
            'cities' => $this->whenLoaded('cities'),
        ];
    }
}
