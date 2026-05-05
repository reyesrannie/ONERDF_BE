<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ItemResource extends JsonResource
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
            "code" => $this->code,
            "description" => $this->description,
            "uom" => $this->uom_id
                ? [
                    "id" => $this->uom->id,
                    "name" => $this->uom->name,
                ]
                : null,
            "system" => $this->system_id
                ? [
                    "id" => $this->system->id,
                    "name" => $this->system->name,
                ]
                : null,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
            "deleted_at" => $this->deleted_at,
        ];
    }
}
