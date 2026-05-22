<?php

namespace App\Livewire\TruckManagement;

use App\Models\Truck;
use App\Models\TruckAssignment;
use App\Models\TruckLog;
use Livewire\Component;
use Livewire\WithPagination;

class TruckShow extends Component
{
    use WithPagination;

    public $truck;

    protected $paginationTheme = 'tailwind';

    public function mount($id)
    {
        $this->truck = Truck::withTrashed()
            ->with(['currentArea', 'currentAssignment.driver.user', 'compartments.fuelType'])
            ->findOrFail($id);
    }

    public function endAssignment($assignmentId)
    {
        $assignment = TruckAssignment::findOrFail($assignmentId);

        if ($assignment->status === 'active') {
            $assignment->update([
                'end_time' => now(),
                'status' => 'completed'
            ]);

            // Update truck status if it's no longer in transit
            if ($this->truck->status === 'in_transit') {
                $this->truck->update(['status' => 'available']);
            }

            // Log the assignment end
            $this->logTruckActivity(
                $this->truck->id,
                'status_change',
                null,
                $this->truck->currentArea->area_name ?? 'Unknown',
                "Assignment ended for truck. Driver: " . ($assignment->driver->user->first_name ?? '') . " " . ($assignment->driver->user->last_name ?? '')
            );

            session()->flash('message', 'Assignment ended successfully.');
        } else {
            session()->flash('error', 'This assignment is already completed.');
        }

        $this->truck->refresh();
    }

    /**
     * Create a truck log entry
     */
    private function logTruckActivity($truckId, $action, $liters = null, $location = null, $remarks = null)
    {
        try {
            return TruckLog::create([
                'truck_id' => $truckId,
                'action' => $action,
                'liters' => $liters,
                'location' => $location,
                'remarks' => $remarks,
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to create truck log', [
                'truck_id' => $truckId,
                'action' => $action,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    public function getAssignmentsProperty()
    {
        return $this->truck->assignments()
            ->with(['driver.user'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }

    public function getTotalCapacityProperty()
    {
        return $this->truck->compartments()->sum('capacity_ltrs');
    }

    public function getTotalAvailableProperty()
    {
        return $this->truck->compartments->sum('available_ltrs');
    }

    public function getTotalLoadedProperty()
    {
        return $this->truck->compartments->sum('loaded_ltrs');
    }

    public function getCompartmentsCountProperty()
    {
        return $this->truck->compartments->count();
    }

    public function getRemainingTruckCapacityProperty()
    {
        return max(0, $this->truck->max_capacity_ltrs - $this->totalCapacity);
    }

    public function render()
    {
        return view('livewire.truck-management.truck-show', [
            'assignments' => $this->assignments,
            'totalCapacity' => $this->totalCapacity,
            'totalAvailable' => $this->totalAvailable,
            'totalLoaded' => $this->totalLoaded,
            'compartmentsCount' => $this->compartmentsCount,
            'remainingTruckCapacity' => $this->remainingTruckCapacity,
        ]);
    }
}