<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AccountResource extends JsonResource
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
            "id_prefix" => $this->id_prefix,
            "id_no" => $this->id_no,
            "first_name" => $this->first_name,
            "middle_name" => $this->middle_name,
            "last_name" => $this->last_name,
            "suffix" => $this->suffix ?? null,
            "user_system" => $this->user_system,
            "username" => $this->username,
            "signature" => $this->signature ?? null,
            "access_permission" => explode(",", $this->access_permission),
            "updated_at" => $this->updated_at,
            "deleted_at" => $this->deleted_at,
        ];
    }
}
