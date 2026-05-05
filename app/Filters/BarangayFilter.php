<?php

namespace App\Filters;

use Essa\APIToolKit\Filters\QueryFilters;

class BarangayFilter extends QueryFilters
{
    protected array $allowedFilters = [];

    protected array $columnSearch = ["name"];
    protected array $allowedSorts = ["name"];
}
