<?php

namespace App\Models;

use Essa\APIToolKit\Filters\Filterable;
use Illuminate\Database\Eloquent\Model;
use App\Filters\SupplierBufferSeverityFilter;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SupplierBufferSeverity extends Model
{
    use Filterable, HasFactory, SoftDeletes;
    protected $table = "supplier_buffer_severity";
    protected string $default_filters = SupplierBufferSeverityFilter::class;
    protected $fillable = ["name"];
}
