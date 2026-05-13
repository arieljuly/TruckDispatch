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

        if ($truck->currentAssignment) {
            session()->flash('error', 'This truck already has an active driver assignment.');
            $this->closeAssignModal();
            return;
        }

        $driver = Driver::find($this->driver_id);
        if ($driver && $driver->currentAssignment) {
            session()->flash('error', 'This driver is already assigned to another truck.');
            $this->closeAssignModal();
            return;
        }

        TruckAssignment::create([
            'truck_id' => $this->selectedTruckId,
            'driver_id' => $this->driver_id,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time ?: null,
            'status' => 'active',
        ]);

        session()->flash('message', 'Driver assigned successfully!');
        $this->closeAssignModal();
        $this->dispatch('refreshTrucks');
    }

    public function deleteTruck($id)
    {
        $truck = Truck::findOrFail($id);

        if ($truck->currentAssignment) {
            session()->flash('error', 'Cannot delete truck with active driver assignment.');
            return;
        }

        $truck->delete();
        session()->flash('message', 'Truck deleted successfully!');
    }

    public function render()
    {
        $trucks = Truck::query()
            ->with(['currentArea', 'currentAssignment.driver.user'])
            ->when($this->search, function ($query) {
                $query->where('truck_name', 'like', '%' . $this->search . '%')
                    ->orWhere('plate_number', 'like', '%' . $this->search . '%');
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->paginate($this->perPage);

        $drivers = Driver::with('user')->where('status', 'available')->get();

        return view('livewire.truck-management.truck-list', [
            'trucks' => $trucks,
            'drivers' => $drivers,
        ]);
    }
}