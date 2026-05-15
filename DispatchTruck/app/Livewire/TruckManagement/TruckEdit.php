<?php

namespace App\Livewire\TruckManagement;

use App\Models\Truck;
use App\Models\Area;
use Livewire\Component;

class TruckEdit extends Component
{
    public $truck_id;
    public $truck_name = '';
    public $plate_number = '';
    public $capacity_ltrs = '';
    public $available_ltrs = '';
    public $current_area_id = '';
    public $status = '';
    public $areas = [];

    protected $rules = [
        'truck_name' => 'required|string|max:255',
        'plate_number' => 'required|string|max:50',
        'capacity_ltrs' => 'required|numeric|min:0',
        'available_ltrs' => 'required|numeric|min:0',
        'current_area_id' => 'nullable|exists:areas,id',
        'status' => 'required|in:available,in-transit,maintenance',
    ];

    protected $messages = [
        'plate_number.unique' => 'This plate number is already registered.',
        'capacity_ltrs.min' => 'Capacity must be a positive number.',
        'available_ltrs.min' => 'Available liters must be a positive number.',
    ];

    public function mount($id)
    {
        $this->truck_id = $id;
        $truck = Truck::findOrFail($id);

        // Load truck data
        $this->truck_name = $truck->truck_name;
        $this->plate_number = $truck->plate_number;
        $this->capacity_ltrs = $truck->capacity_ltrs;
        $this->available_ltrs = $truck->available_ltrs;
        $this->current_area_id = $truck->current_area_id;
        $this->status = $truck->status; // This will be one of: 'available', 'in-transit', 'maintenance'

        // Load all active areas
        $this->areas = Area::where('status', 'active')
            ->orderBy('area_name')
            ->get();
    }

    public function updatedCapacityLtrs($value)
    {
        // If available liters exceed the new capacity, adjust it
        if (!empty($this->available_ltrs) && floatval($this->available_ltrs) > floatval($value)) {
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

    public function updateTruck()
    {
        // Validate unique plate number except current truck
        $this->rules['plate_number'] = 'required|string|max:50|unique:trucks,plate_number,' . $this->truck_id;

        // Additional validation
        if (floatval($this->available_ltrs) > floatval($this->capacity_ltrs)) {
            $this->addError('available_ltrs', 'Available liters cannot exceed capacity liters.');
            return;
        }

        $this->validate();

        $truck = Truck::findOrFail($this->truck_id);
        $truck->update([
            'truck_name' => $this->truck_name,
            'plate_number' => $this->plate_number,
            'capacity_ltrs' => $this->capacity_ltrs,
            'available_ltrs' => $this->available_ltrs,
            'current_area_id' => $this->current_area_id ?: null,
            'status' => $this->status, // This will be one of: 'available', 'in-transit', 'maintenance'
        ]);

        session()->flash('message', 'Truck updated successfully!');
        return redirect()->route('admin.trucks.index');
    }

    public function render()
    {
        return view('livewire.truck-management.truck-edit');
    }
}