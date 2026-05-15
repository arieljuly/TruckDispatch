<?php

namespace App\Livewire\TruckManagement;

use App\Models\Truck;
use App\Models\Area;
use App\Models\TruckLog;
use Livewire\Component;

class TruckCreate extends Component
{
    public $truck_name = '';
    public $plate_number = '';
    public $capacity_ltrs = '';
    public $available_ltrs = '';
    public $current_area_id = '';
    public $status = 'available';
    public $areas = [];

    protected $rules = [
        'truck_name' => 'required|string|max:255',
        'plate_number' => 'required|string|max:50|unique:trucks,plate_number',
        'capacity_ltrs' => 'required|numeric|min:0',
        'available_ltrs' => 'required|numeric|min:0',
        'current_area_id' => 'nullable|exists:areas,id',
        'status' => 'required|in:available,in-transit,maintenance',
    ];

    protected $messages = [
        'capacity_ltrs.min' => 'Capacity must be a positive number.',
        'available_ltrs.min' => 'Available liters must be a positive number.',
        'plate_number.unique' => 'This plate number is already registered.',
    ];

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

    public function mount()
    {
        $this->areas = Area::where('status', 'active')->orderBy('area_name')->get();
    }

    public function updatedCapacityLtrs($value)
    {
        // If available liters is empty or exceeds new capacity, adjust it
        if (empty($this->available_ltrs) || floatval($this->available_ltrs) > floatval($value)) {
            $this->available_ltrs = $value;
        }
    }

    public function updatedAvailableLtrs($value)
    {
        // Check if available exceeds capacity
        if (!empty($this->capacity_ltrs) && floatval($value) > floatval($this->capacity_ltrs)) {
            $this->addError('available_ltrs', 'Available liters cannot exceed capacity liters.');
        } else {
            $this->resetErrorBag('available_ltrs');
        }
    }

    public function createTruck()
    {
        // Additional check for available vs capacity
        if (floatval($this->available_ltrs) > floatval($this->capacity_ltrs)) {
            $this->addError('available_ltrs', 'Available liters cannot exceed capacity liters.');
            return;
        }

        $this->validate();

        $truck = Truck::create([
            'truck_name' => $this->truck_name,
            'plate_number' => $this->plate_number,
            'capacity_ltrs' => floatval($this->capacity_ltrs),
            'available_ltrs' => floatval($this->available_ltrs),
            'current_area_id' => $this->current_area_id ?: null,
            'status' => $this->status,
        ]);

        // Log truck creation with area name as location
        $areaName = $this->current_area_id ? Area::find($this->current_area_id)->area_name : 'Not assigned';
        $this->logTruckActivity(
            $truck->id,
            'created',
            floatval($this->capacity_ltrs),
            $areaName, // Location is the area name
            "New truck registered: {$this->truck_name} | Plate: {$this->plate_number} | Capacity: {$this->capacity_ltrs}L | Available: {$this->available_ltrs}L | Status: {$this->status}"
        );

        session()->flash('message', 'Truck created successfully!');
        return redirect()->route('admin.trucks.index');
    }

    public function render()
    {
        return view('livewire.truck-management.truck-create');
    }
}