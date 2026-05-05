<?php

namespace App\Models;

use Essa\APIToolKit\Filters\Filterable;
use Illuminate\Database\Eloquent\Model;
use App\Filters\SupplierReferenceFilter;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SupplierReference extends Model
{
    use Filterable, HasFactory, SoftDeletes;
    protected $table = "supplier_references";
    protected string $default_filters = SupplierReferenceFilter::class;
    protected $fillable = ["name"];
}
