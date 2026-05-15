<?php

namespace App\Livewire\TruckManagement;

use App\Models\Truck;
use App\Models\Driver;
use App\Models\TruckAssignment;
use Livewire\Component;
use Livewire\WithPagination;

class TruckList extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $perPage = 10;
    public $showAssignModal = false;
    public $selectedTruckId = null;
    public $showInactive = false; // Add toggle for showing inactive trucks

    // Assignment form fields
    public $driver_id = '';
    public $start_time = '';
    public $end_time = '';

    protected $listeners = ['refreshTrucks' => '$refresh'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openAssignModal($truckId)
    {
        $this->selectedTruckId = $truckId;
        $this->showAssignModal = true;
        $this->driver_id = '';
        $this->start_time = now()->format('Y-m-d\TH:i');
        $this->end_time = '';
    }

    public function closeAssignModal()
    {
        $this->showAssignModal = false;
        $this->selectedTruckId = null;
        $this->reset(['driver_id', 'start_time', 'end_time']);
    }

    public function assignDriver()
    {
        $this->validate([
            'driver_id' => 'required|exists:drivers,id',
            'start_time' => 'required|date',
            'end_time' => 'nullable|date|after:start_time',
        ]);

        $truck = Truck::findOrFail($this->selectedTruckId);

        // Check if truck already has an assignment
        if ($truck->currentAssignment) {
            session()->flash('error', 'This truck already has an active driver assignment.');
            $this->closeAssignModal();
            return;
        }

        // Check if driver is still available
        $driver = Driver::find($this->driver_id);
        if ($driver && $driver->currentAssignment) {
            session()->flash('error', 'This driver is already assigned to another truck.');
            $this->closeAssignModal();
            return;
        }

        // Create the assignment
        TruckAssignment::create([
            'truck_id' => $this->selectedTruckId,
            'driver_id' => $this->driver_id,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time ?: null,
            'status' => 'active',
        ]);

        // Update truck status to 'in-transit'
        $truck->update(['status' => 'in-transit']);

        // Update driver status to 'on-duty'
        $driver->update(['status' => 'on-duty']);

        session()->flash('message', 'Driver assigned successfully!');
        $this->closeAssignModal();
        $this->dispatch('refreshTrucks');
    }

    public function deleteTruck($id)
    {
        $truck = Truck::findOrFail($id);

        // Check if truck has active assignment
        if ($truck->currentAssignment) {
            session()->flash('error', 'Cannot delete truck with active driver assignment. Please end the assignment first.');
            return;
        }

        // Mark the truck inactive before soft deleting it.
        $truck->update(['status' => 'inactive']);

        // Soft delete the truck (sets deleted_at timestamp)
        $truck->delete();

        session()->flash('message', 'Truck moved to inactive successfully!');
    }

    public function restoreTruck($id)
    {
        $truck = Truck::withTrashed()->findOrFail($id);
        $truck->restore();

        session()->flash('message', 'Truck restored successfully!');
    }

    public function forceDeleteTruck($id)
    {
        $truck = Truck::withTrashed()->findOrFail($id);

        // Check if truck has assignments
        if ($truck->assignments()->exists()) {
            session()->flash('error', 'Cannot permanently delete truck with assignment history.');
            return;
        }

        // Permanently delete
        $truck->forceDelete();

        session()->flash('message', 'Truck permanently deleted successfully!');
    }

    public function render()
    {
        $query = Truck::query()
            ->with(['currentArea', 'currentAssignment.driver.user']);

        // Show inactive trucks if toggle is on
        if ($this->showInactive) {
            $query->withTrashed();
        } else {
            $query->whereNull('deleted_at');
        }

        $trucks = $query
            ->when($this->search, function ($query) {
                $query->where('truck_name', 'like', '%' . $this->search . '%')
                    ->orWhere('plate_number', 'like', '%' . $this->search . '%');
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->orderBy('id', 'desc')
            ->paginate($this->perPage);

        // Fetch only available drivers (no active assignments)
        $drivers = Driver::with('user')
            ->whereHas('user', function ($query) {
                $query->where('status', 'active')
                    ->where('role_id', 3);
            })
            ->where('status', 'available')
            ->whereDoesntHave('currentAssignment')
            ->orderBy('id')
            ->get();

        return view('livewire.truck-management.truck-list', [
            'trucks' => $trucks,
            'drivers' => $drivers,
        ]);
    }
}