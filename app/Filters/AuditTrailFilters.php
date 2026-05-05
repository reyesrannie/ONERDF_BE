<?php

namespace App\Filters;

use Essa\APIToolKit\Filters\QueryFilters;

class AuditTrailFilters extends QueryFilters
{
    protected array $allowedFilters = [];

    protected array $columnSearch = [
        "user_id",
        "system_id",
        "action",
        "module",
        "details",
    ];

    protected array $allowedSorts = [
        "id",
        "user_id",
        "system_id",
        "action",
        "module",
        "details",
        "created_at",
    ];
}
