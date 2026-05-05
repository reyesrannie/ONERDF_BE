<?php

namespace App\Filters;

use Essa\APIToolKit\Filters\QueryFilters;

class SupplierTypeFilter extends QueryFilters
{
    protected array $allowedFilters = [];

    protected array $columnSearch = ["name", "id"];

    protected array $allowedSorts = ["name", "id", "updated_at", "deleted_at"];
}
