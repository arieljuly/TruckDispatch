<?php

namespace App\Livewire\DriverManagement;

use App\Models\Driver;
use App\Models\TruckAssignment;
use Livewire\Component;

class DriverShow extends Component
{
    public $driver;
    public $assignments;

    public function mount($id)
    {
        $this->driver = Driver::with(['user', 'currentAssignment.truck'])->findOrFail($id);
        $this->assignments = TruckAssignment::with(['truck'])
            ->where('driver_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function render()
    {
        return view('livewire.driver-management.driver-show');
    }
}