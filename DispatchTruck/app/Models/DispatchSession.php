<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DispatchSession extends Model
{
    protected $table = "dispatch_sessions";
    protected $fillable = [
        'algorithm_used',
        'total_demand',
        'total_supply',
        'status', // pending, executed, failed
        'notes',
    ];
}
