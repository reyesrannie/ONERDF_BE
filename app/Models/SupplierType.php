<?php

namespace App\Models;

use App\Filters\SupplierTypeFilter;
use Essa\APIToolKit\Filters\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SupplierType extends Model
{
    use Filterable, HasFactory, SoftDeletes;
    protected $table = "supplier_types";
    protected string $default_filters = SupplierTypeFilter::class;
    protected $fillable = ["name"];
}
