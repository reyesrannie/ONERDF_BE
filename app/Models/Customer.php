<?php

namespace App\Models;

use App\Filters\CustomerFilters;
use Essa\APIToolKit\Filters\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Customer extends Model
{
    use HasFactory, Filterable, SoftDeletes;

    protected $table = "customer";

    protected string $default_filters = CustomerFilters::class;

    protected $fillable = [
        "sync_id",
        "code",
        "name",
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
        "last_updated_by",
    ];
}
