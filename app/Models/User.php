<?php

namespace App\Models;

use App\Shared\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'assigned_pos',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
        ];
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === UserRole::SUPERADMIN;
    }

    public function isAdminSar(): bool
    {
        return in_array($this->role, [UserRole::SUPERADMIN, UserRole::ADMIN_SAR], true);
    }

    public function isKasir(): bool
    {
        return in_array($this->role, [UserRole::SUPERADMIN, UserRole::KASIR], true);
    }

    public function isPetugasPos(): bool
    {
        return in_array($this->role, [UserRole::SUPERADMIN, UserRole::ADMIN_SAR, UserRole::PETUGAS_POS], true);
    }
}
