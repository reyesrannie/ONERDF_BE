<?php

namespace App\Models;

use App\Filters\RegionFilter;
use Essa\APIToolKit\Filters\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Region extends Model
{
    use Filterable, SoftDeletes;

    protected $table = "region";

    protected $fillable = [
        "psgc_id",
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

    protected string $default_filters = RegionFilter::class;

    public function province()
    {
        return $this->hasMany(Province::class, "region_id", "psgc_id");
    }

    protected function casts(): array
    {
        return [
            "psgc_id" => "string",
        ];
    }
}
