<?php

namespace App\Models;

use App\Filters\DepartmentFilter;
use Essa\APIToolKit\Filters\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Department extends Model
{
    use Filterable, HasFactory, SoftDeletes;

    protected string $default_filters = DepartmentFilter::class;
    protected $table = "department";

    protected $fillable = ["code", "name", "last_update_by"];

    public function oneChargings()
    {
        return $this->hasMany(ChargingOfAccounts::class);
    }

    protected static function booted()
    {
        static::updated(function ($department) {
            $department->oneChargings()->touch();
        });
    }
}
