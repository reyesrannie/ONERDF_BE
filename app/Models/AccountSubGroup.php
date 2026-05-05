<?php

namespace App\Models;

use App\Filters\AccountSubGroupFilters;
use Essa\APIToolKit\Filters\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AccountSubGroup extends Model
{
    use HasFactory, Filterable, SoftDeletes;

    protected $table = "account_sub_group";

    protected string $default_filters = AccountSubGroupFilters::class;

    protected $fillable = ["name", "last_updated_by"];
}
