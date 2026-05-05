<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SupplierResource extends JsonResource
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
            "name" => $this->name,
            "address" => $this->address,
            "terms" => $this->terms,
            "supplier_system" => $this->supplier_system,
            "supplier_type" => $this->supplier_buffer_id
                ? [
                    "id" => $this->supplier_type->id,
                    "name" => $this->supplier_type->name,
                ]
                : null,
            "supplier_buffer" => $this->supplier_buffer_id
                ? [
                    "id" => $this->supplier_buffer->id,
                    "name" => $this->supplier_buffer->name,
                ]
                : null,
            "supplier_reference" => $this->supplier_reference_id
                ? [
                    "id" => $this->supplier_reference->id,
                    "name" => $this->supplier_reference->name,
                ]
                : null,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
            "deleted_at" => $this->deleted_at,
        ];
    }
}
