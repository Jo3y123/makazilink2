<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'address', 'county', 'town', 'type',
        'owner_id', 'agent_id', 'description', 'is_active',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function units()
    {
        return $this->hasMany(Unit::class);
    }
}