<?php

namespace App\Models;

use App\Filters\OTPLogsFilters;
use Essa\APIToolKit\Filters\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class OtpLogs extends Model
{
    use HasFactory, Notifiable, SoftDeletes, Filterable;

    protected string $default_filters = OTPLogsFilters::class;

    protected $fillable = ["user_id", "accessed_by", "system_name", "status"];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, "user_id");
    }

    public function accessedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, "accessed_by");
    }
}
