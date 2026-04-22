<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'receipt_number', 'invoice_id', 'tenant_id', 'unit_id',
        'amount', 'payment_method', 'reference_number',
        'mpesa_transaction_id', 'payment_date', 'status', 'notes',
        'whatsapp_sent', 'whatsapp_sent_at', 'recorded_by',
    ];

    protected function casts(): array
    {
        return [
            'payment_date'     => 'date',
            'whatsapp_sent'    => 'boolean',
            'whatsapp_sent_at' => 'datetime',
            'amount'           => 'decimal:2',
        ];
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public static function generateReceiptNumber(): string
    {
        $last = static::latest()->first();
        $next = $last ? ((int) substr($last->receipt_number, 4)) + 1 : 1;
        return 'RCP-' . str_pad($next, 5, '0', STR_PAD_LEFT);
    }
}