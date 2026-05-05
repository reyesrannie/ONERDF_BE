<?php

namespace App\Models;

use App\Filters\CreditFilters;
use Essa\APIToolKit\Filters\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Credit extends Model
{
    use HasFactory, Filterable, SoftDeletes;

    protected $table = "credit";

    protected string $default_filters = CreditFilters::class;

    protected $fillable = ["code", "name", "last_updated_by"];
}
