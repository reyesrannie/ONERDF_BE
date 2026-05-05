<?php

namespace App\Models;

use App\Filters\ChargeSyncFilters;
use Essa\APIToolKit\Filters\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ChargeSync extends Model
{
    use Filterable, HasFactory, SoftDeletes;

    protected string $default_filters = ChargeSyncFilters::class;

    protected $table = "charge_sync";

    protected $fillable = ["system_name","url_holder", "token", "last_updated_by"];
}
