<?php

namespace App\Models;

use App\Filters\CategoryFilter;
use Essa\APIToolKit\Filters\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use Filterable, HasFactory, SoftDeletes;

    protected $table = "category";

    protected string $default_filters = CategoryFilter::class;

    protected $fillable = ["name", "last_update_by"];
}
