<?php

namespace App\Livewire\DriverManagement;

use App\Models\Driver;
use Livewire\Component;
use Livewire\Attributes\Rule;

class DriverEdit extends Component
{
    public $driver_id;
    public $driver;

    #[Rule('required|string|max:50|unique:drivers,licensed_number')]
    public $licensed_number = '';

    #[Rule('required|in:available,on-duty,off-duty')]
    public $status = '';

    public function mount($id)
    {
        $this->driver_id = $id;
        $this->driver = Driver::with('user')->findOrFail($id);
        $this->licensed_number = $this->driver->licensed_number;
        $this->status = $this->driver->status;
    }

    public function update()
    {
        $this->validate();

        $driver = Driver::findOrFail($this->driver_id);

        // Check if license number is being changed and if it's unique
        if ($this->licensed_number !== $driver->licensed_number) {
            $this->validate([
                'licensed_number' => 'required|string|max:50|unique:drivers,licensed_number',
            ]);
        }

        $driver->update([
            'licensed_number' => $this->licensed_number,
            'status' => $this->status,
        ]);

        session()->flash('message', 'Driver updated successfully!');
        return redirect()->route('admin.drivers.index');
    }

    public function render()
    {
        return view('livewire.driver-management.driver-edit');
    }
}