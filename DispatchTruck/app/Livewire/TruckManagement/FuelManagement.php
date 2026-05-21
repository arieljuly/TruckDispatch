<?php

namespace App\Livewire\TruckManagement;

use App\Models\Truck;
use App\Models\Area;
use App\Models\TruckLog;
use Livewire\Component;

class FuelManagement extends Component
{

    public function render()
    {
        return view('livewire.truck-management.fuel-management');
    }
}