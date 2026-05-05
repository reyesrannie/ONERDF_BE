<?php

namespace App\Models;

use App\Filters\SystemFilters;
use Essa\APIToolKit\Filters\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class System extends Model
{
    use Filterable, HasFactory, SoftDeletes;

    protected string $default_filters = SystemFilters::class;

    protected $fillable = [
        "url_holder",
        "token",
        "system_image",
        "system_name",
        "system_background",
        "backend_url",
        "category_id",
        "last_update_by",
        "slice",
        "description",
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
