<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupportAccessToken extends Model
{
    use HasUuids;

    protected $fillable = [
        "user_id",
        "requested_by_id",
        "created_by",
        "otp",
        "expires_at",
        "used_at",
    ];

    protected $casts = [
        "expires_at" => "datetime",
        "used_at" => "datetime",
    ];

    public function isValid(): bool
    {
        return is_null($this->used_at) && $this->expires_at->isFuture();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, "user_id");
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, "created_by");
    }
}
