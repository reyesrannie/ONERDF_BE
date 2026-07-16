<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ItemResourceExternal extends JsonResource
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
                    "code" => $this->uom->code,
                    "description" => $this->uom->description,
                    "is_integer" => $this->uom->is_integer,
                ]
                : null,

            "account_title" => $this->item_account_titles->map(function (
                $pivotItem
            ) {
                return [
                    "id" => $pivotItem->account_title->id,
                    "code" => $pivotItem->account_title?->code,
                    "name" => $pivotItem->account_title?->name,
                ];
            }),

            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
            "deleted_at" => $this->deleted_at,
        ];
    }
}
