<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Area;

class Truck extends Model
{
    protected $table = "trucks";
    protected $fillable = [
        'truck_name',
        'plate_number',
        'capacity_ltrs',
        'available_ltrs',
        'current_area_id', 
        'status', //available, in-transit, maintenance
    ];  
    
    public function currentArea()
    {
        return $this->belongsTo(Area::class, 'current_area_id');
    }   
}
