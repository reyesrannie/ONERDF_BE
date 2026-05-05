<?php

namespace App\Models;

use App\Filters\NotificationFilters;
use Essa\APIToolKit\Filters\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Notification extends Model
{
    use HasFactory, SoftDeletes, Filterable;

    protected string $default_filters = NotificationFilters::class;

    protected $fillable = [
        "title",
        "memo_file",
        "subtitle",
        "content",
        "footer",
        "last_update_by",
    ];
}
