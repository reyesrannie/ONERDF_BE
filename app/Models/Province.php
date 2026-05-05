<?php

namespace App\Models;

use App\Filters\ProvinceFilter;
use Essa\APIToolKit\Filters\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Province extends Model
{
    use Filterable, SoftDeletes;

    protected $table = "province";

    protected $fillable = [
        "psgc_id",
        "region_id",
        "name",
        "correspondence_code",
        "geographic_level",
        "old_names",
        "city_class",
        "income_classification",
        "urban_rural",
        "population",
        "status",
    ];

    protected string $default_filters = ProvinceFilter::class;

    public function region()
    {
        return $this->belongsTo(Region::class, "region_id", "psgc_id");
    }

    protected function casts(): array
    {
        return [
            "psgc_id" => "string",
        ];
    }
}
