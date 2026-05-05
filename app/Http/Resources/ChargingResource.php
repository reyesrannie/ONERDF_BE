<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChargingResource extends JsonResource
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

            "company_id" => $this->company->id ?? null,
            "company_code" => $this->company->code ?? null,
            "company_name" => $this->company->name ?? null,

            "business_unit_id" => $this->business_unit->id ?? null,
            "business_unit_code" => $this->business_unit->code ?? null,
            "business_unit_name" => $this->business_unit->name ?? null,

            "department_id" => $this->department->id ?? null,
            "department_code" => $this->department->code ?? null,
            "department_name" => $this->department->name ?? null,

            "unit_id" => $this->department_unit->id ?? null,
            "unit_code" => $this->department_unit->code ?? null,
            "unit_name" => $this->department_unit->name ?? null,

            "sub_unit_id" => $this->sub_unit->id ?? null,
            "sub_unit_code" => $this->sub_unit->code ?? null,
            "sub_unit_name" => $this->sub_unit->name ?? null,

            "location_id" => $this->location->id ?? null,
            "location_code" => $this->location->code ?? null,
            "location_name" => $this->location->name ?? null,

            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
            "deleted_at" => $this->deleted_at,
        ];
    }
}
