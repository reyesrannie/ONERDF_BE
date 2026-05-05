<?php

namespace App\Filters;

use Essa\APIToolKit\Filters\QueryFilters;

class SupplierFilter extends QueryFilters
{
    protected array $allowedFilters = [];

    protected array $columnSearch = [
        "code",
        "name",
        "terms",
        "supplier_type_id",
        "supplier_buffer_id",
        "supplier_reference_id",
    ];

    protected array $allowedSorts = [
        "id",
        "code",
        "name",
        "address",
        "terms",
        "supplier_type_id",
        "supplier_buffer_id",
        "supplier_reference_id",
        "updated_at",
        "deleted_at",
    ];
}
