<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaseAlert extends Model
{
    use HasFactory;

    protected $fillable = [
        'lease_id', 'alert_type', 'sent', 'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'sent'     => 'boolean',
            'sent_at'  => 'datetime',
        ];
    }

    public function lease()
    {
        return $this->belongsTo(Lease::class);
    }
}