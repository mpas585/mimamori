<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class AdminUser extends Authenticatable
{
    protected $table = 'admin_users';

    protected $fillable = [
        'email',
        'password_hash',
        'name',
        'role',
        'last_login_at',
    ];

    protected $hidden = [
        'password_hash',
    ];

    /**
     * 認証用パスワードカラム
     */
    public function getAuthPassword(): string
    {
        return $this->password_hash;
    }
}
