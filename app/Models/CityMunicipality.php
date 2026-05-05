<?php

namespace App\Models;

use App\Filters\CityMunicipalityFilter;
use Essa\APIToolKit\Filters\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CityMunicipality extends Model
{
    use Filterable, SoftDeletes;

    protected $table = "city_municipality";

    protected $fillable = [
        "psgc_id",
        "province_id",
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

    protected string $default_filters = CityMunicipalityFilter::class;

    public function region()
    {
        return $this->belongsTo(Region::class, "region_id", "psgc_id");
    }

    public function province()
    {
        return $this->belongsTo(Province::class, "province_id", "psgc_id");
    }

    protected function casts(): array
    {
        return [
            "psgc_id" => "string",
        ];
    }
}
