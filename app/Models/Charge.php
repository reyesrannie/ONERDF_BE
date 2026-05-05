<?php

namespace App\Models;

use App\Filters\ChargeFilter;
use Essa\APIToolKit\Filters\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Charge extends Model
{
    use Filterable, HasFactory, SoftDeletes;

    protected $table = "charge";

    protected string $default_filters = ChargeFilter::class;

    protected $fillable = ["name", "last_update_by"];
}
