<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Lease extends Model
{
    use HasFactory;

    protected $fillable = [
        'unit_id', 'tenant_id', 'start_date', 'end_date',
        'monthly_rent', 'deposit_paid', 'status',
        'next_due_date', 'notice_days', 'terms',
    ];

    protected function casts(): array
    {
        return [
            'start_date'    => 'date',
            'end_date'      => 'date',
            'next_due_date' => 'date',
            'monthly_rent'  => 'decimal:2',
            'deposit_paid'  => 'decimal:2',
        ];
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function alerts()
    {
        return $this->hasMany(LeaseAlert::class);
    }

    public function getDaysUntilExpiryAttribute(): ?int
    {
        if (!$this->end_date) return null;
        return (int) now()->diffInDays($this->end_date, false);
    }

    public function isExpiringSoon(int $days = 30): bool
    {
        $expiry = $this->days_until_expiry;
        return $expiry !== null && $expiry >= 0 && $expiry <= $days;
    }
}