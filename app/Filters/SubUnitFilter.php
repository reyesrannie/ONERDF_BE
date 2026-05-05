<?php

namespace App\Filters;

use Essa\APIToolKit\Filters\QueryFilters;

class SubUnitFilter extends QueryFilters
{
    protected array $allowedFilters = [];

    protected array $columnSearch = ["code", "name"];
    protected array $allowedSorts = ["code", "name", "id"];
}
