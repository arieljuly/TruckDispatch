<?php

namespace App\Livewire\TruckManagement;

use App\Models\FuelType;
use Livewire\Component;

class FuelCreate extends Component
{
    public $fuel_code = '';
    public $fuel_name = '';
    public $status = 'active';

    protected $rules = [
        'fuel_code' => 'required|string|max:50|unique:fuel_types,fuel_code',
        'fuel_name' => 'required|string|max:100',
        'status' => 'required|in:active,inactive',
    ];

    protected $messages = [
        'fuel_code.required' => 'Fuel code is required.',
        'fuel_code.unique' => 'This fuel code already exists.',
        'fuel_name.required' => 'Fuel name is required.',
    ];

    public function createFuel()
    {
        $this->validate();

        FuelType::create([
            'fuel_code' => strtoupper($this->fuel_code),
            'fuel_name' => $this->fuel_name,
            'status' => $this->status,
        ]);

        session()->flash('message', 'Fuel type created successfully.');
        session()->flash('message_type', 'success');

        return redirect()->route('admin.fuel.index');
    }

    public function render()
    {
        return view('livewire.truck-management.fuel-create');
    }
}