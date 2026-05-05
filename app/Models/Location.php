<?php

namespace App\Models;

use App\Filters\LocationFilter;
use Essa\APIToolKit\Filters\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Location extends Model
{
    use Filterable, HasFactory, SoftDeletes;

    protected $table = "location";

    protected string $default_filters = LocationFilter::class;

    protected $fillable = ["code", "name", "last_update_by"];
}
