<?php

namespace App\Filters;

use Essa\APIToolKit\Filters\QueryFilters;

class CustomerFilters extends QueryFilters
{
    protected array $allowedFilters = [];

    protected array $columnSearch = [
        "name",
        "code",
        "id",
        "business_name",
        "registration_status",
        "contact_no",
        "email_address",
        "house_no",
        "street_name",
        "barangay_name",
        "city",
        "province",
        "customer_type",
        "cluster_id",
        "cluster_name",
        "terms",
    ];

    protected array $allowedSorts = [
        "name",
        "code",
        "id",
        "business_name",
        "registration_status",
        "contact_no",
        "email_address",
        "house_no",
        "street_name",
        "barangay_name",
        "city",
        "province",
        "customer_type",
        "cluster_id",
        "cluster_name",
        "terms",
    ];
}
