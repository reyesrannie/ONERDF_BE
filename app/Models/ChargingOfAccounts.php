<?php

namespace App\Models;

use App\Filters\ChargingFilters;
use Essa\APIToolKit\Filters\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ChargingOfAccounts extends Model
{
    use Filterable, HasFactory, SoftDeletes;

    protected string $default_filters = ChargingFilters::class;

    protected $table = "charging_of_accounts";

    protected $fillable = [
        "code",
        "name",
        "company_id",
        "business_unit_id",
        "department_id",
        "department_unit_id",
        "sub_unit_id",
        "location_id",
        "last_update_by",
    ];

    protected $hidden = [
        "company_id",
        "business_unit_id",
        "department_id",
        "department_unit_id",
        "sub_unit_id",
        "location_id",
        "last_update_by",
    ];

    public function company()
    {
        return $this->belongsTo(
            Company::class,
            "company_id",
            "id"
        )->withTrashed();
    }
    public function business_unit()
    {
        return $this->belongsTo(
            BusinessUnit::class,
            "business_unit_id",
            "id"
        )->withTrashed();
    }
    public function department()
    {
        return $this->belongsTo(
            Department::class,
            "department_id",
            "id"
        )->withTrashed();
    }
    public function department_unit()
    {
        return $this->belongsTo(
            DepartmentUnit::class,
            "department_unit_id",
            "id"
        )->withTrashed();
    }
    public function sub_unit()
    {
        return $this->belongsTo(
            SubUnit::class,
            "sub_unit_id",
            "id"
        )->withTrashed();
    }
    public function location()
    {
        return $this->belongsTo(
            Location::class,
            "location_id",
            "id"
        )->withTrashed();
    }
}
