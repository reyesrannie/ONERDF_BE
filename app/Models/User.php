<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Filters\AccountFilters;
use Laravel\Sanctum\HasApiTokens;
use Essa\APIToolKit\Filters\Filterable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, Filterable;

    protected string $default_filters = AccountFilters::class;

    protected $fillable = [
        "id",
        "id_prefix",
        "id_no",
        "first_name",
        "middle_name",
        "last_name",
        "suffix",
        "username",
        "password",
        "signature",
        "access_permission",
        "last_update_by",
    ];

    protected $hidden = ["password", "remember_token"];

    public function user_system()
    {
        return $this->hasMany(UserSystem::class);
    }
}
