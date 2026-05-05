<?php

namespace App\Models;

use App\Models\SupplierType;
use App\Filters\SupplierFilter;
use App\Models\SupplierSystems;
use App\Models\SupplierReference;
use App\Models\SupplierBufferSeverity;
use Essa\APIToolKit\Filters\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Supplier extends Model
{
    use Filterable, HasFactory, SoftDeletes;

    protected $table = "suppliers";

    protected string $default_filters = SupplierFilter::class;

    protected $fillable = [
        "code",
        "name",
        "address",
        "terms",
        "supplier_type_id",
        "supplier_buffer_id",
        "supplier_reference_id",
    ];

    public function supplier_system()
    {
        return $this->hasMany(SupplierSystems::class);
    }

    public function supplier_type()
    {
        return $this->belongsTo(SupplierType::class, "supplier_type_id", "id");
    }

    public function supplier_buffer()
    {
        return $this->belongsTo(
            SupplierBufferSeverity::class,
            "supplier_buffer_id",
            "id"
        );
    }

    public function supplier_reference()
    {
        return $this->belongsTo(
            SupplierReference::class,
            "supplier_reference_id",
            "id"
        );
    }
}
