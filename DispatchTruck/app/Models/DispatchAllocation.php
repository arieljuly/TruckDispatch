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
        'liters_allocated',  // Changed from 'allocated_liters'
        'distance_used',
        'is_primary_area',
        'status', // pending, completed, failed
    ];

    // Add an accessor for backward compatibility
    public function getAllocatedLitersAttribute()
    {
        return $this->liters_allocated;
    }

    // Add a mutator for backward compatibility
    public function setAllocatedLitersAttribute($value)
    {
        $this->attributes['liters_allocated'] = $value;
    }

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