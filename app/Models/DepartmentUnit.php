<?php

namespace App\Models;

use App\Filters\DepartmentUnitFilter;
use Essa\APIToolKit\Filters\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DepartmentUnit extends Model
{
    use Filterable, HasFactory, SoftDeletes;

    protected $table = "department_unit";

    protected string $default_filters = DepartmentUnitFilter::class;

    protected $fillable = ["code", "name", "last_update_by"];
}
