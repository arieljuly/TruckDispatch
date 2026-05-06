<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TruckAssignment extends Model
{
    protected $table = "truck_assignments";
    protected $fillable = [
        'truck_id',
        'driver_id',
        'start_time',
        'end_time', 
        'status', //active, completed, cancelled
    ];
}
