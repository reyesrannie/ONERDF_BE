<?php

namespace App\Models;

use App\Filters\CompanyFilter;
use Essa\APIToolKit\Filters\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use Filterable, HasFactory, SoftDeletes;

    protected string $default_filters = CompanyFilter::class;

    protected $table = "companies";

    protected $fillable = ["code", "name", "last_update_by"];

    public function oneChargings()
    {
        return $this->hasMany(ChargingOfAccounts::class);
    }

    protected static function booted()
    {
        static::updated(function ($company) {
            $company->oneChargings()->touch();
        });
    }
}
