<?php

namespace App\Livewire\TruckManagement;

use App\Models\Truck;
use App\Models\Area;
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

        Truck::create([
            'truck_name' => $this->truck_name,
            'plate_number' => $this->plate_number,
            'capacity_ltrs' => floatval($this->capacity_ltrs),
            'available_ltrs' => floatval($this->available_ltrs),
            'current_area_id' => $this->current_area_id ?: null,
            'status' => $this->status, // This will be 'available', 'in-transit', or 'maintenance'
        ]);

        session()->flash('message', 'Truck created successfully!');
        return redirect()->route('admin.trucks.index');
    }

    public function render()
    {
        return view('livewire.truck-management.truck-create');
    }
}