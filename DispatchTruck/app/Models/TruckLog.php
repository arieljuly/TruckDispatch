<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TruckLog extends Model
{
    protected $table = "truck_logs";

    protected $fillable = [
        'truck_id',
        'action',
        'liters',
        'location',
        'remarks',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function truck()
    {
        return $this->belongsTo(Truck::class, 'truck_id')->withTrashed(); // Include soft deleted trucks
    }

    public function area()
    {
        return $this->belongsTo(Area::class, 'area_id');
    }
}