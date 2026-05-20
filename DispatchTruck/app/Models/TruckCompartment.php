<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TruckCompartment extends Model
{
    use SoftDeletes;
    protected $table = "truck_compartments";
    protected $fillable = [
        'truck_id',
        'current_fuel_type_id',
        'compartment_no',
        'capacity_ltrs',
        'loaded_ltrs',
        'available_ltrs',
    ];

    protected $casts = [
        'capacity_ltrs' => 'decimal:3',
        'loaded_ltrs' => 'decimal:3',
        'available_ltrs' => 'decimal:3',
    ];

    // Relationships
    public function truck()
    {
        return $this->belongsTo(Truck::class, 'truck_id');
    }

    public function fuelType()
    {
        return $this->belongsTo(FuelType::class, 'current_fuel_type_id');
    }

    public function dispatchAllocationItems()
    {
        return $this->hasMany(DispatchAllocationItem::class, 'truck_compartment_id');
    }

    // Helper to calculate available liters (if not synced automatically)
    public function calculateAvailableLiters()
    {
        return $this->capacity_ltrs - $this->loaded_ltrs;
    }

    // Helper to check if compartment has enough space
    public function hasAvailableSpace($liters)
    {
        return $this->available_ltrs >= $liters;
    }
}