<?php

namespace App\Livewire\TruckManagement;

use App\Models\Truck;
use App\Models\TruckAssignment;
use App\Models\TruckLog;
use App\Models\Driver;
use Livewire\Component;

class TruckShow extends Component
{
    public $truck;
    public $assignments;

    private function logTruckActivity($truckId, $action, $liters = null, $location = null, $remarks = null)
    {
        return TruckLog::create([
            'truck_id' => $truckId,
            'action' => $action,
            'liters' => $liters,
            'location' => $location,
            'remarks' => $remarks,
        ]);
    }

    public function mount($id)
    {
        $this->truck = Truck::with(['currentArea', 'currentAssignment.driver.user'])->findOrFail($id);
        $this->assignments = TruckAssignment::with(['driver.user'])
            ->where('truck_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function endAssignment($assignmentId)
    {
        $assignment = TruckAssignment::with(['truck', 'driver'])->findOrFail($assignmentId);

        $driverName = $assignment->driver ?
            "{$assignment->driver->user->first_name} {$assignment->driver->user->last_name}" :
            'Unknown driver';

        // Get area name for location
        $areaName = $assignment->truck->currentArea ? $assignment->truck->currentArea->area_name : 'Unknown location';

        // Log driver unassigned with area name as location
        $this->logTruckActivity(
            $assignment->truck_id,
            'driver_unassigned',
            null,
            $areaName, // Location is the area name
            "Driver {$driverName} unassigned from truck"
        );

        // Update assignment
        $assignment->update([
            'end_time' => now(),
            'status' => 'completed'
        ]);

        // Log assignment completion with area name as location
        $this->logTruckActivity(
            $assignment->truck_id,
            'returned',
            null,
            $areaName, // Location is the area name
            "Truck returned after delivery"
        );

        // Update truck status to 'available'
        if ($assignment->truck) {
            $assignment->truck->update(['status' => 'available']);

            // Log status change with area name as location
            $this->logTruckActivity(
                $assignment->truck_id,
                'status_change',
                null,
                $areaName, // Location is the area name
                "Status changed from in-transit to available after assignment ended"
            );
        }

        // Update driver status back to 'available'
        if ($assignment->driver) {
            $assignment->driver->update(['status' => 'available']);
        }

        session()->flash('message', 'Assignment ended successfully!');
        return redirect()->route('admin.trucks.show', $this->truck->id);
    }

    public function render()
    {
        return view('livewire.truck-management.truck-show');
    }
}