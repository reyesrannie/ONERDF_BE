<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SystemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "url_holder" => $this->url_holder,
            "system_image" => $this->system_image,
            "system_name" => $this->system_name,
            "system_background" => $this->system_background,
            "description" => $this->description,
            "backend_url" => $this->backend_url,
            "token" => $this->token,
            "slice" => json_decode($this->slice),
            "category" => $this->category,
            "updated_at" => $this->updated_at,
            "deleted_at" => $this->deleted_at,
        ];
    }
}
