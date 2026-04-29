<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RenewalCode extends Model
{
    protected $fillable = [
        'mpesa_code',
        'phone_number',
        'amount',
        'plan',
        'days_activated',
        'used_at',
    ];

    protected $casts = [
        'used_at' => 'datetime',
        'amount'  => 'decimal:2',
    ];

    public static function isUsed(string $code): bool
    {
        return static::where('mpesa_code', strtoupper($code))->exists();
    }

    public static function markUsed(string $code, string $phone, float $amount, string $plan, int $days): void
    {
        static::create([
            'mpesa_code'      => strtoupper($code),
            'phone_number'    => $phone,
            'amount'          => $amount,
            'plan'            => $plan,
            'days_activated'  => $days,
            'used_at'         => now(),
        ]);
    }
}