<?php

namespace App\Filters;

use Essa\APIToolKit\Filters\QueryFilters;

class ColumnFilter extends QueryFilters
{
    protected array $allowedFilters = [];

    protected array $columnSearch = ["name", "id"];

    protected array $allowedSorts = ["name", "id"];
}
