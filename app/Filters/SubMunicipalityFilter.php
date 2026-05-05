<?php

namespace App\Filters;

use Essa\APIToolKit\Filters\QueryFilters;

class SubMunicipalityFilter extends QueryFilters
{
    protected array $allowedFilters = [];

    protected array $columnSearch = ["name"];
    protected array $allowedSorts = ["name"];
}
