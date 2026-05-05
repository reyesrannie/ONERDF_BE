<?php

namespace App\Filters;

use Essa\APIToolKit\Filters\QueryFilters;

class ItemFilter extends QueryFilters
{
    protected array $allowedFilters = [];

    protected array $columnSearch = ["code", "description", "uom_id"];

    protected array $allowedSorts = ["id", "code", "description", "uom_id"];
}
