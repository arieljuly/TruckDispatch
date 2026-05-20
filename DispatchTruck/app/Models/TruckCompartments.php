<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TruckCompartments extends Model
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
}
