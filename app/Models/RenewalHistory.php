<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RenewalHistory extends Model
{
    protected $table = 'renewal_history';

    protected $fillable = [
        'days_added',
        'activated_from',
        'activated_to',
        'activated_by',
        'method',
        'notes',
    ];

    protected $casts = [
        'activated_from' => 'date',
        'activated_to'   => 'date',
    ];
}