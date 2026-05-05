<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AccountTitleResource extends JsonResource
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
            "account_type_name" => $this->account_type->name ?? null,
            "account_group_name" => $this->account_group->name ?? null,
            "account_sub_group_name" => $this->account_sub_group->name ?? null,
            "financial_statement_name" =>
                $this->financial_statement->name ?? null,
            "normal_balance_name" => $this->normal_balance->name ?? null,
            "allocation_name" => $this->allocation->name ?? null,
            "account_unit_name" => $this->account_unit->name ?? null,
            "charge" => $this->charge->name ?? null,
            "created_at" => $this->created_at ?? null,
            "updated_at" => $this->updated_at ?? null,
            "deleted_at" => $this->deleted_at ?? null,
        ];
    }
}
