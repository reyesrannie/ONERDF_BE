<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasswordManager extends Model
{
    use HasFactory;

    protected $fillable = ["id_prefix", "id_no", "password_encrypted"];
}
