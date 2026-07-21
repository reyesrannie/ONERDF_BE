<?php

namespace App\Filters;

use Essa\APIToolKit\Filters\QueryFilters;

class OTPLogsFilters extends QueryFilters
{
    protected array $allowedFilters = [];

    protected array $columnSearch = [
        "user_id",
        "system_name",
        "accessed_by",
        "status",
    ];

    protected array $allowedSorts = [
        "id",
        "user_id",
        "system_name",
        "accessed_by",
        "status",
        "created_at",
    ];
}
