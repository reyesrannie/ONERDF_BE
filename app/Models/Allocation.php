<?php

namespace App\Models;

use App\Filters\AllocationFilters;
use Essa\APIToolKit\Filters\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Allocation extends Model
{
    use Filterable, HasFactory, SoftDeletes;

    protected $table = "allocation";

    protected string $default_filters = AllocationFilters::class;

    protected $fillable = ["name", "last_update_by"];
}
