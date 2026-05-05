<?php

namespace App\Filters;

use Essa\APIToolKit\Filters\QueryFilters;

class ChargingFilters extends QueryFilters
{
    protected array $allowedFilters = [];

    protected array $columnSearch = ["code", "name"];
    protected array $allowedSorts = [
        "code",
        "name",
        "id",
        "company_id",
        "business_unit_id",
        "department_id",
        "department_unit_id",
        "sub_unit_id",
        "location_id",
        "last_update_by",
    ];
}
