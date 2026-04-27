<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;

    protected $fillable = [
    'property_id', 'unit_number', 'type', 'rent_amount', 'deposit_amount',
    'status', 'floor_number', 'notes', 'has_water_meter', 'water_meter_number',
    'image_path',
    ];

    protected function casts(): array
    {
        return [
            'has_water_meter' => 'boolean',
            'rent_amount'     => 'decimal:2',
            'deposit_amount'  => 'decimal:2',
        ];
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function leases()
    {
        return $this->hasMany(Lease::class);
    }

    public function activeLease()
    {
        return $this->hasOne(Lease::class)->where('status', 'active')->latest();
    }

    public function maintenanceRequests()
    {
        return $this->hasMany(MaintenanceRequest::class);
    }

    public function waterReadings()
    {
        return $this->hasMany(WaterReading::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}