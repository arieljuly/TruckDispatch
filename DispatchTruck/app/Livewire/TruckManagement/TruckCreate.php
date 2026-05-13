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

    public $showCreateModal = true;
    public $areas = [];

    protected $rules = [
        'truck_name' => 'required|string|max:255',
        'plate_number' => 'required|string|max:50|unique:trucks,plate_number',
        'capacity_ltrs' => 'required|numeric|min:0',
        'available_ltrs' => 'required|numeric|min:0',
        'current_area_id' => 'nullable|exists:areas,id',
        'status' => 'required|in:available,in-transit,maintenance',
    ];

    public function mount()
    {
        $this->areas = Area::all();
        $this->available_ltrs = $this->capacity_ltrs;
    }

    public function updatedCapacityLtrs($value)
    {
        // When capacity changes, set available liters to the same value if it's empty or if it's greater than new capacity
        if (empty($this->available_ltrs) || $this->available_ltrs > $value) {
            $this->available_ltrs = $value;
        }
    }

    public function createTruck()
    {
        $this->validate();

        Truck::create([
            'truck_name' => $this->truck_name,
            'plate_number' => $this->plate_number,
            'capacity_ltrs' => $this->capacity_ltrs,
            'available_ltrs' => $this->available_ltrs,
            'current_area_id' => $this->current_area_id ?: null,
            'status' => $this->status,
        ]);

        session()->flash('message', 'Truck created successfully!');
        return redirect()->route('admin.trucks.index');
    }

    public function render()
    {
        return view('livewire.truck-management.truck-create');
    }
}