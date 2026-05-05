<?php

namespace App\Filters;

use Essa\APIToolKit\Filters\QueryFilters;

class UomFilter extends QueryFilters
{
    protected array $allowedFilters = [];

    protected array $columnSearch = ["id", "code", "description"];

    protected array $allowedSorts = ["code", "description", "id"];
}
