<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Salary extends Model
{
    protected $fillable = [
        'staff_name',
        'role',
        'amount',
        'payment_date',
        'month_year',
        'payment_method',
        'reference',
        'notes',
        'recorded_by',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount'       => 'decimal:2',
    ];

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public static function totalForMonth(int $month, int $year): float
    {
        return (float) static::whereMonth('payment_date', $month)
            ->whereYear('payment_date', $year)
            ->sum('amount');
    }
}