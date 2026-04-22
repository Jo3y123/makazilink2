<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WaterReading extends Model
{
    use HasFactory;

    protected $fillable = [
        'unit_id', 'previous_reading', 'current_reading', 'units_consumed',
        'rate_per_unit', 'amount_charged', 'reading_date', 'billing_period',
        'recorded_by', 'notes',
    ];

    protected function casts(): array
    {
        return ['reading_date' => 'date'];
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}