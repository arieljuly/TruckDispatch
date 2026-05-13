<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Truck extends Model
{
    use SoftDeletes;

    protected $table = "trucks";
    protected $fillable = [
        'truck_name',
        'plate_number',
        'capacity_ltrs',
        'available_ltrs',
        'current_area_id',
        'status', // available, in-transit, maintenance
    ];

    protected $casts = [
        'capacity_ltrs' => 'decimal:2',
        'available_ltrs' => 'decimal:2',
    ];

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
}