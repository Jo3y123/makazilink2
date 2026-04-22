<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number', 'lease_id', 'tenant_id', 'unit_id',
        'rent_amount', 'water_amount', 'garbage_amount', 'other_amount',
        'total_amount', 'amount_paid', 'balance', 'due_date',
        'period_start', 'period_end', 'status', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'due_date'     => 'date',
            'period_start' => 'date',
            'period_end'   => 'date',
        ];
    }

    public function lease()
    {
        return $this->belongsTo(Lease::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public static function generateNumber(): string
    {
    $last = static::orderBy('id', 'desc')->first();

    if (!$last) {
        return 'INV-00001';
    }

    $lastNumber = (int) substr($last->invoice_number, 4);
    $next       = $lastNumber + 1;

    return 'INV-' . str_pad($next, 5, '0', STR_PAD_LEFT);
    }
}