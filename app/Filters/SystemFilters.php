<?php

namespace App\Filters;

use Essa\APIToolKit\Filters\QueryFilters;

class SystemFilters extends QueryFilters
{
    protected array $allowedFilters = [
        "system_name",
        "url_holder",
        "updated_at",
    ];

    protected array $columnSearch = ["system_name"];

    protected array $allowedSorts = [
        "system_name",
        "url_holder",
        "updated_at",
        "id",
    ];
}
