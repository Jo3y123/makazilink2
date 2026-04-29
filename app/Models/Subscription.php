<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Subscription extends Model
{
    protected $fillable = [
    'client_name',
    'client_email',
    'client_phone',
    'plan',
    'status',
    'trial_ends_at',
    'expires_at',
    'max_units',
    'monthly_fee',
    'notes',
    'is_exempt',
    ];

    protected $casts = [
    'trial_ends_at' => 'date',
    'expires_at'    => 'date',
    'monthly_fee'   => 'decimal:2',
    'is_exempt'     => 'boolean',
    ];

    public function isActive(): bool
    {
        if ($this->status === 'active' && $this->expires_at && $this->expires_at->isFuture()) {
            return true;
        }

        if ($this->status === 'trial' && $this->trial_ends_at && $this->trial_ends_at->isFuture()) {
            return true;
        }

        return false;
    }

    public function isExpired(): bool
    {
        return !$this->isActive();
    }

    public function daysRemaining(): int
    {
        if ($this->status === 'trial' && $this->trial_ends_at) {
            return max(0, now()->diffInDays($this->trial_ends_at, false));
        }

        if ($this->status === 'active' && $this->expires_at) {
            return max(0, now()->diffInDays($this->expires_at, false));
        }

        return 0;
    }

    public function planLabel(): string
    {
        return match($this->plan) {
            'starter'    => 'Starter (up to 20 units)',
            'growth'     => 'Growth (up to 50 units)',
            'pro'        => 'Pro (up to 100 units)',
            'enterprise' => 'Enterprise (unlimited)',
            default      => ucfirst($this->plan),
        };
    }
}