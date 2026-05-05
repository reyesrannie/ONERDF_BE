<?php

namespace App\Models;

use App\Filters\AccountGroupFilters;
use Essa\APIToolKit\Filters\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AccountGroup extends Model
{
    use HasFactory, Filterable, SoftDeletes;

    protected $table = "account_group";

    protected string $default_filters = AccountGroupFilters::class;

    protected $fillable = ["name", "last_updated_by"];
}
