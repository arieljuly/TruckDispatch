<?php

namespace App\Livewire\TruckManagement;

use App\Models\Truck;
use App\Models\TruckAssignment;
use App\Models\Driver;
use Livewire\Component;

class TruckShow extends Component
{
    public $truck;
    public $assignments;

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

        // Update assignment
        $assignment->update([
            'end_time' => now(),
            'status' => 'completed'
        ]);

        // Update truck status to 'available'
        if ($assignment->truck) {
            $assignment->truck->update(['status' => 'available']);
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