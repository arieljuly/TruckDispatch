<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Maintenance extends Model
{
    protected $table = "maintenance_logs";
    protected $fillable = [
        'truck_id',
        'reported_by', // who reported the issue, can be null if reported by system
        'completed_by', // who performed the maintenance, can be null if not completed  
        'maintenance_type', // 'oil_change', 'repair', 'inspection', 'tire_replacement', 'brake_service','engine_service','transmission_service','electrical_repair','body_repair','regular_maintenance'
        'title',
        'description',
        'scheduled_date',
        'start_date',
        'completed_date',
        'odometer_reading',
        'estimated_cost',
        'actual_cost',
        'status', // 'scheduled', 'in_progress', 'completed', 'cancelled', 'delayed'
        'priority', // 'low', 'medium', 'high', 'emergency'
    ];
    public function truck()
    {
        return $this->belongsTo(Truck::class, 'truck_id'); 
    }
    public function reporter()
    {
        return $this->belongsTo(User::class, 'reported_by'); 
    }
    public function completer()
    {
        return $this->belongsTo(User::class, 'completed_by');
    }
}
