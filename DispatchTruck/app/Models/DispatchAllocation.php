<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DispatchAllocation extends Model
{
    protected $table = "dispatch_allocations";
    protected $fillable = [
        'dispatch_session_id',
        'truck_id',
        'area_id',
        'allocated_liters',
        'distance_used',
        'is_primary_area', 
        'status', //pending, completed, failed
    ];

    public function dispatchSession()
    {
        return $this->belongsTo(DispatchSession::class, 'dispatch_session_id');
    }

    public function truck()
    {
        return $this->belongsTo(Truck::class, 'truck_id');
    }

    public function area()
    {
        return $this->belongsTo(Area::class, 'area_id');
    }
}
