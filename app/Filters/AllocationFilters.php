<?php

namespace App\Filters;

use Essa\APIToolKit\Filters\QueryFilters;

class AllocationFilters extends QueryFilters
{
    protected array $allowedFilters = [];

    protected array $columnSearch = ["name"];

    protected array $allowedSorts = ["name", "id"];
}
