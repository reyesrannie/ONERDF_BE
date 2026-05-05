<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserSystem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ["user_id", "system_id"];

    public function system()
    {
        return $this->belongsTo(
            System::class,
            "system_id",
            "id"
        )->withTrashed();
    }
}
