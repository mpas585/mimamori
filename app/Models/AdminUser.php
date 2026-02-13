<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminUser extends Authenticatable
{
    protected $table = 'admin_users';

    protected $fillable = [
        'email',
        'password_hash',
        'name',
        'role',
        'organization_id',
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

    /**
     * マスター管理者か
     */
    public function isMaster(): bool
    {
        return $this->role === 'master';
    }

    /**
     * 組織管理者か
     */
    public function isOperator(): bool
    {
        return $this->role === 'operator';
    }

    /**
     * 所属組織
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
