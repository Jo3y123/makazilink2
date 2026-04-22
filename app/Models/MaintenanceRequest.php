<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'unit_id', 'tenant_id', 'assigned_to', 'title', 'description',
        'category', 'priority', 'status', 'cost', 'resolved_at', 'resolution_notes',
    ];

    protected function casts(): array
    {
        return [
            'resolved_at' => 'date',
            'cost'        => 'decimal:2',
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

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function photos()
    {
        return $this->hasMany(MaintenancePhoto::class);
    }
}