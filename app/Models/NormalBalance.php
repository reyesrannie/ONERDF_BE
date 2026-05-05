<?php

namespace App\Models;

use App\Filters\NormalBalanceFilters;
use Essa\APIToolKit\Filters\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NormalBalance extends Model
{
    use HasFactory, Filterable, SoftDeletes;

    protected $table = "normal_balance";

    protected string $default_filters = NormalBalanceFilters::class;

    protected $fillable = ["name", "last_updated_by"];
}
