<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AccountTitleViewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "account_title" => [
                "id" => $this->id,
                "code" => $this->code,
                "nae" => $this->name,
            ],
            "account_type" => [
                "id" => $this->account_type->id ?? null,
                "name" => $this->account_type->name ?? null,
            ],

            "account_group" => [
                "id" => $this->account_group->id ?? null,
                "name" => $this->account_group->name ?? null,
            ],

            "account_sub_group" => [
                "id" => $this->account_sub_group->id ?? null,
                "name" => $this->account_sub_group->name ?? null,
            ],
            "financial_statement" => [
                "id" => $this->financial_statement->id ?? null,
                "name" => $this->financial_statement->name ?? null,
            ],

            "normal_balance" => [
                "id" => $this->normal_balance->id ?? null,
                "name" => $this->normal_balance->name ?? null,
            ],
            "allocation" => [
                "id" => $this->allocation->id ?? null,
                "name" => $this->allocation->name ?? null,
            ],

            "charge" => [
                "id" => $this->charge->id ?? null,
                "name" => $this->charge->name ?? null,
            ],
            "account_unit" => [
                "id" => $this->account_unit->id ?? null,
                "name" => $this->account_unit->name ?? null,
            ],

            "credit" => [
                "id" => $this->credit->id ?? null,
                "name" => $this->credit->name ?? null,
            ],

            "created_at" => $this->created_at ?? null,
            "updated_at" => $this->updated_at ?? null,
            "deleted_at" => $this->deleted_at ?? null,
        ];
    }
}
