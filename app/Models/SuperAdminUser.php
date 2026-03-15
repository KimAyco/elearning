<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuperAdminUser extends Model
{
    use HasFactory;

    protected $table = 'super_admin_users';

    protected $fillable = [
        'full_name',
        'email',
        'password_hash',
        'status',
        'last_login_at',
    ];

    protected $hidden = [
        'password_hash',
    ];

    protected function casts(): array
    {
        return [
            'last_login_at' => 'datetime',
        ];
    }
}

