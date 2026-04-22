<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenancePhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'maintenance_request_id', 'file_path', 'caption', 'photo_type',
    ];

    public function request()
    {
        return $this->belongsTo(MaintenanceRequest::class, 'maintenance_request_id');
    }
}