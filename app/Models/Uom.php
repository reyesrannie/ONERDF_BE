<?php

namespace App\Models;

use App\Filters\UomFilter;
use Essa\APIToolKit\Filters\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Uom extends Model
{
    use Filterable, HasFactory, SoftDeletes;
    protected $table = "uom";
    protected string $default_filters = UomFilter::class;
    protected $fillable = ["code", "description", "is_integer"];
}
