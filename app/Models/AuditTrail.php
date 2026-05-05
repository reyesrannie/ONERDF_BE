<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use App\Filters\AuditTrailFilters;
use Essa\APIToolKit\Filters\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AuditTrail extends Model
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, Filterable;

    protected string $default_filters = AuditTrailFilters::class;

    protected $table = "audit_trail";

    protected $fillable = [
        "user_id",
        "system_id",
        "action",
        "module",
        "details",
        "payload",
        "status",
    ];

    public function system()
    {
        return $this->belongsTo(System::class, "system_id", "id");
    }
}
