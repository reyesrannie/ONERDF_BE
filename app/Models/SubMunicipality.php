<?php

namespace App\Models;

use App\Filters\SubMunicipalityFilter;
use Essa\APIToolKit\Filters\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SubMunicipality extends Model
{
    use Filterable, SoftDeletes;

    protected $table = "sub_municipality";

    protected $fillable = [
        "psgc_id",
        "city_municipality_id",
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

    protected string $default_filters = SubMunicipalityFilter::class;

    public function cityMunicipality()
    {
        return $this->belongsTo(
            CityMunicipality::class,
            "city_municipality_id",
            "psgc_id"
        );
    }

    protected function casts(): array
    {
        return [
            "psgc_id" => "string",
        ];
    }
}
