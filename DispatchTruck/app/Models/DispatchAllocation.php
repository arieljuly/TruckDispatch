<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DispatchAllocation extends Model
{
    use SoftDeletes;

    protected $table = "dispatch_allocations";

    protected $fillable = [
        'dispatch_session_id',
        'truck_id',
        'area_id',
        'liters_allocated',
        'distance_used',
        'is_primary_area',
        'status', // planned, in_progress, completed, cancelled
    ];

    protected $casts = [
        'is_primary_area' => 'boolean',
        'liters_allocated' => 'decimal:3',
        'distance_used' => 'decimal:2',
    ];

    // Accessor for backward compatibility
    public function getAllocatedLitersAttribute()
    {
        return $this->liters_allocated;
    }

    // Mutator for backward compatibility
    public function setAllocatedLitersAttribute($value)
    {
        $this->attributes['liters_allocated'] = $value;
    }

    // Relationships
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

    // A dispatch allocation has many items (stops)
    public function items()
    {
        return $this->hasMany(DispatchAllocationItems::class, 'dispatch_allocation_id');
    }

    // Helper to get total delivered liters
    public function getTotalDeliveredLitersAttribute()
    {
        return $this->items()->where('status', 'delivered')->sum('liters_allocated');
    }

    // Helper to check if allocation is fully delivered
    public function getIsFullyDeliveredAttribute()
    {
        return $this->total_delivered_liters >= $this->liters_allocated;
    }
}