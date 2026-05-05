<?php

namespace App\Filters;

use Essa\APIToolKit\Filters\QueryFilters;

class ProvinceFilter extends QueryFilters
{
    protected array $allowedFilters = [];

    protected array $columnSearch = ["psgc_id", "name"];
    protected array $allowedSorts = ["psgc_id", "name"];

    protected array $relationSearch = [
        "region" => ["psgc_id"],
    ];
}
