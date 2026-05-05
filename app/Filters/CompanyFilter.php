<?php

namespace App\Filters;

use Essa\APIToolKit\Filters\QueryFilters;

class CompanyFilter extends QueryFilters
{
    protected array $allowedFilters = ["created_at", "id"];

    protected array $columnSearch = ["code", "name"];
    protected array $allowedSorts = ["code", "name", "id"];
}
