<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;

class Truck extends Model
{
    use SoftDeletes;

    protected $table = "trucks";

    protected $fillable = [
        'truck_name',
        'plate_number',
        'current_area_id',
        'max_capacity_ltrs',
        'status',
    ];

    protected $casts = [
        'current_area_id' => 'integer',
        'status' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->whereNull('deleted_at');
    }

    public function scopeInactive($query)
    {
        return $query->whereNotNull('deleted_at');
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    public function scopeInTransit($query)
    {
        return $query->where('status', 'in_transit');
    }

    public function scopeUnderMaintenance($query)
    {
        return $query->where('status', 'maintenance');
    }

    // Relationships
    public function currentArea()
    {
        return $this->belongsTo(Area::class, 'current_area_id');
    }

    public function currentAssignment()
    {
        return $this->hasOne(TruckAssignment::class)->where('status', 'active')->latest('start_time');
    }

    public function assignments()
    {
        return $this->hasMany(TruckAssignment::class);
    }

    public function logs()
    {
        return $this->hasMany(TruckLog::class);
    }

    public function dispatchSessions()
    {
        return $this->hasMany(DispatchSession::class, 'assigned_truck_id');
    }

    public function recommendedDispatchSessions()
    {
        return $this->hasMany(DispatchSession::class, 'recommended_truck_id');
    }

    public function compartments()
    {
        return $this->hasMany(TruckCompartment::class);
    }

    // Helper methods for compartments
    public function getTotalCapacityAttribute()
    {
        return $this->compartments()->sum('capacity_ltrs');
    }

    public function getTotalLoadedLitersAttribute()
    {
        return $this->compartments()->sum('loaded_ltrs');
    }

    public function getTotalAvailableLitersAttribute()
    {
        return $this->compartments()->sum('available_ltrs');
    }

    public function getFuelLevelPercentageAttribute()
    {
        $totalCapacity = $this->total_capacity;
        if ($totalCapacity <= 0) {
            return 0;
        }

        $totalAvailable = $this->total_available_liters;
        return ($totalAvailable / $totalCapacity) * 100;
    }

    // Check if truck has any compartments
    public function hasCompartments(): bool
    {
        return $this->compartments()->exists();
    }

    // Get compartments grouped by fuel type
    public function getCompartmentsByFuelType()
    {
        return $this->compartments()
            ->with('fuelType')
            ->get()
            ->groupBy('current_fuel_type_id');
    }

    // Get summary of fuel types in truck
    public function getFuelTypesSummaryAttribute()
    {
        $summary = [];
        foreach ($this->compartments as $compartment) {
            $fuelTypeName = $compartment->fuelType->name ?? 'Unknown';
            if (!isset($summary[$fuelTypeName])) {
                $summary[$fuelTypeName] = [
                    'total_capacity' => 0,
                    'total_available' => 0,
                    'compartments' => []
                ];
            }
            $summary[$fuelTypeName]['total_capacity'] += $compartment->capacity_ltrs;
            $summary[$fuelTypeName]['total_available'] += $compartment->available_ltrs;
            $summary[$fuelTypeName]['compartments'][] = $compartment->compartment_no;
        }
        return $summary;
    }

    public function __toString()
    {
        return $this->truck_name . ' (' . $this->plate_number . ')';
    }
}