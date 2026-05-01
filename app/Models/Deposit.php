<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Deposit extends Model
{
    protected $fillable = [
        'tenant_id',
        'lease_id',
        'amount_expected',
        'amount_received',
        'date_received',
        'status',
        'deduction_amount',
        'deduction_reason',
        'refund_amount',
        'refund_date',
        'refund_method',
        'refund_reference',
        'notes',
        'recorded_by',
    ];

    protected $casts = [
        'date_received'   => 'date',
        'refund_date'     => 'date',
        'amount_expected' => 'decimal:2',
        'amount_received' => 'decimal:2',
        'deduction_amount'=> 'decimal:2',
        'refund_amount'   => 'decimal:2',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function lease()
    {
        return $this->belongsTo(Lease::class);
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function balanceHeld(): float
    {
        return (float) $this->amount_received - (float) $this->deduction_amount - (float) $this->refund_amount;
    }

    public function isFullyRefunded(): bool
    {
        return $this->balanceHeld() <= 0 && $this->refund_amount > 0;
    }
}