<?php

namespace App\Models;

use App\Filters\BusinessUnitFilter;
use Essa\APIToolKit\Filters\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BusinessUnit extends Model
{
    use Filterable, HasFactory, SoftDeletes;

    protected string $default_filters = BusinessUnitFilter::class;

    protected $table = "business_unit";

    protected $fillable = ["code", "name", "last_update_by"];

    public function oneChargings()
    {
        return $this->hasMany(ChargingOfAccounts::class);
    }

    protected static function booted()
    {
        static::updated(function ($businessUnit) {
            $businessUnit->oneChargings()->touch();
        });
    }
}
