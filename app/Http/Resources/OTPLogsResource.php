<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OTPLogsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $accessedBy = is_object($this->accessedBy)
            ? $this->accessedBy
            : (is_object($this->accessed_by)
                ? $this->accessed_by
                : null);

        $user = is_object($this->user) ? $this->user : null;

        $formatName = function ($person) {
            if (!$person) {
                return null;
            }

            return trim(
                implode(
                    " ",
                    array_filter([
                        $person->first_name,
                        $person->middle_name,
                        $person->last_name,
                    ])
                )
            );
        };

        return [
            "id" => $this->id,
            "accessed_by" => $formatName($this->accessedBy),
            "system_name" => $this->system_name,
            "status" => $this->status,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
            "user" => $formatName($this->user),
        ];
    }
}
