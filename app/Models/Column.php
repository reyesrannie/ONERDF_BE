<?php

namespace App\Models;

use App\Filters\ColumnFilter;
use Essa\APIToolKit\Filters\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Column extends Model
{
    use Filterable, HasFactory, SoftDeletes;
    protected $table = "columns";
    protected string $default_filters = ColumnFilter::class;
    protected $fillable = ["name"];
}
