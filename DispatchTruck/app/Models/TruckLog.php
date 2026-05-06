<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TruckLog extends Model
{
    protected $table = "truck_logs";
    protected $fillable = [
        'truck_id',
        'action', //loaded, delivered, maintenance
        'liters',
        'location',
        'remarks',
    ];

    public function truck()
    {
        return $this->belongsTo(Truck::class, 'truck_id');
    }

    public function area()
    {
        return $this->belongsTo(Area::class, 'area_id');
    }
}
