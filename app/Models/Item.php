<?php

namespace App\Models;

use App\Filters\ItemFilter;
use App\Models\ItemSystem;
use App\Models\Uom;
use Essa\APIToolKit\Filters\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use Filterable, HasFactory, SoftDeletes;

    protected $table = "item";

    protected string $default_filters = ItemFilter::class;

    protected $fillable = ["code", "description", "uom_id"];

    public function item_system()
    {
        return $this->hasMany(ItemSystem::class);
    }

    public function item_account_titles()
    {
        return $this->hasMany(ItemAccountTitle::class);
    }

    public function uom()
    {
        return $this->belongsTo(Uom::class, "uom_id", "id");
    }
}
