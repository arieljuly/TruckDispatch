<?php

namespace App\Livewire\TruckManagement;

use App\Models\FuelType;
use Livewire\Component;

class FuelEdit extends Component
{
    public $fuel_id;
    public $fuel_code = '';
    public $fuel_name = '';
    public $status = '';

    protected $rules = [
        'fuel_code' => 'required|string|max:50',
        'fuel_name' => 'required|string|max:100',
        'status' => 'required|in:active,inactive',
    ];

    public function mount($id)
    {
        $this->fuel_id = $id;
        $fuel = FuelType::findOrFail($id);

        $this->fuel_code = $fuel->fuel_code;
        $this->fuel_name = $fuel->fuel_name;
        $this->status = $fuel->status;
    }

    public function updateFuel()
    {
        $fuel = FuelType::findOrFail($this->fuel_id);

        $this->rules['fuel_code'] = 'required|string|max:50|unique:fuel_types,fuel_code,' . $this->fuel_id;

        $this->validate();

        $fuel->update([
            'fuel_code' => strtoupper($this->fuel_code),
            'fuel_name' => $this->fuel_name,
            'status' => $this->status,
        ]);

        session()->flash('message', 'Fuel type updated successfully.');
        session()->flash('message_type', 'success');

        return redirect()->route('admin.fuel.index');
    }

    public function render()
    {
        return view('livewire.truck-management.fuel-edit');
    }
}