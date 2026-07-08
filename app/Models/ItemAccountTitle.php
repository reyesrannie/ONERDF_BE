<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ItemAccountTitle extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ["item_id", "account_title_id"];

    public function account_title()
    {
        return $this->belongsTo(
            AccountTitle::class,
            "account_title_id",
            "id"
        )->withTrashed();
    }
}
