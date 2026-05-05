<?php

namespace App\Models;

use App\Models\System;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ItemSystem extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = "item_system";
    protected $fillable = ["item_id", "system_id"];

    public function system()
    {
        return $this->belongsTo(
            System::class,
            "system_id",
            "id"
        )->withTrashed();
    }
}
