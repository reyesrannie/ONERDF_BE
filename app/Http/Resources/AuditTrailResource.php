<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuditTrailResource extends JsonResource
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
            "user_id" => $this->user_id,
            "system" => $this->system,
            "action" => $this->action,
            "module" => $this->module,
            "details" => $this->details,
            "created_at" => $this->created_at,
        ];
    }
}
