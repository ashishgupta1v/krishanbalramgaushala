<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class AdminUser extends Model
{
    use HasUuids;

    protected $table = 'admin_users';

    protected $fillable = [
        'name', 'username', 'password', 'is_active', 'last_login_at'
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'is_active'     => 'boolean',
        'last_login_at' => 'datetime',
    ];
}
