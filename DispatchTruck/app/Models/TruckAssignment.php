<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TruckAssignment extends Model
{
    protected $table = "truck_assignments";

    protected $fillable = [
        'truck_id',
        'driver_id',
        'start_time',
        'end_time',
        'status', // active, completed, cancelled
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function truck()
    {
        return $this->belongsTo(Truck::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    // Auto update truck and driver status when assignment is created, updated, or deleted
    protected static function booted()
    {
        // When assignment is created (active)
        static::created(function ($assignment) {
            if ($assignment->status === 'active') {
                if ($assignment->truck) {
                    $assignment->truck->update(['status' => 'in_transit']);
                }
                if ($assignment->driver) {
                    $assignment->driver->update(['status' => 'on_duty']);
                }
            }
        });

        // When assignment status changes
        static::updated(function ($assignment) {
            $originalStatus = $assignment->getOriginal('status');
            $newStatus = $assignment->status;

            // If changed from active to completed/cancelled
            if ($originalStatus === 'active' && in_array($newStatus, ['completed', 'cancelled'])) {
                if ($assignment->truck) {
                    $assignment->truck->update(['status' => 'available']);
                }
                if ($assignment->driver) {
                    $assignment->driver->update(['status' => 'available']);
                }
            }
            // If changed to active from something else
            elseif ($newStatus === 'active' && $originalStatus !== 'active') {
                if ($assignment->truck) {
                    $assignment->truck->update(['status' => 'in_transit']);
                }
                if ($assignment->driver) {
                    $assignment->driver->update(['status' => 'on_duty']);
                }
            }
        });

        // When assignment is deleted
        static::deleted(function ($assignment) {
            if ($assignment->status === 'active') {
                if ($assignment->truck) {
                    $assignment->truck->update(['status' => 'available']);
                }
                if ($assignment->driver) {
                    $assignment->driver->update(['status' => 'available']);
                }
            }
        });
    }
}