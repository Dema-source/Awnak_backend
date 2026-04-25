<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DocumentResource extends JsonResource
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
            'path' => $this->path,
            'type' => $this->type,
            'original_name' => $this->original_name,
            'documentable_type' => $this->documentable_type,
            'documentable_id' => $this->documentable_id,
            'documentable' => $this->whenLoaded('documentable'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'file_url' => asset('storage/' . $this->path),
        ];
    }
}
