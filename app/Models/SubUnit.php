<?php

namespace App\Models;

use App\Filters\SubUnitFilter;
use Essa\APIToolKit\Filters\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SubUnit extends Model
{
    use Filterable, HasFactory, SoftDeletes;

    protected $table = "sub_unit";

    protected string $default_filters = SubUnitFilter::class;

    protected $fillable = ["code", "name", "last_update_by"];

    public function oneChargings()
    {
        return $this->hasMany(ChargingOfAccounts::class);
    }

    protected static function booted()
    {
        static::updated(function ($subUnit) {
            $subUnit->oneChargings()->touch();
        });
    }
}
