<?php

namespace App\Models;

use App\Filters\AccountUnitFilters;
use Essa\APIToolKit\Filters\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AccountUnit extends Model
{
    use HasFactory, Filterable, SoftDeletes;

    protected $table = "account_units";

    protected string $default_filters = AccountUnitFilters::class;

    protected $fillable = ["name", "last_updated_by"];
}
