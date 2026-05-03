<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_active'         => 'boolean',
        ];
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, ['admin', 'superadmin']);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'superadmin';
    }

    public function isAgent(): bool
    {
        return $this->role === 'agent';
    }

    public function isAccountant(): bool
    {
        return $this->role === 'accountant';
    }

    public function isCaretaker(): bool
    {
        return $this->role === 'caretaker';
    }

    public function isTenant(): bool
    {
        return $this->role === 'tenant';
    }

    public function hasRole(string|array $roles): bool
    {
    // Superadmin has all roles
    if ($this->role === 'superadmin') {
        return true;
    }

    if (is_string($roles)) {
        return $this->role === $roles;
    }

    return in_array($this->role, $roles);
    }

    public function properties()
    {
        return $this->hasMany(Property::class, 'owner_id');
    }

    public function tenant()
    {
        return $this->hasOne(Tenant::class);
    }
}