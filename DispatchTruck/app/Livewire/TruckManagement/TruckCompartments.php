<?php

namespace App\Livewire\TruckManagement;

use Livewire\Component;

class TruckCompartments extends Component
{
    public function mount()
    {
        // Add debug to see if component is being instantiated
        \Log::info('TruckCompartments component mounted');
    }

    public function render()
    {
        \Log::info('TruckCompartments render method called');

        return view('livewire.truck-management.truck-compartments');
    }
}