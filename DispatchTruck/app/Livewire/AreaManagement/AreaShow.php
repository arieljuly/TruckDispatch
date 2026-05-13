<?php

namespace App\Livewire\AreaManagement;

use App\Models\Area;
use App\Services\NominatimService;
use Livewire\Component;

class AreaShow extends Component
{
    public $area;
    public $address;

    public function mount($id)
    {
        $this->area = Area::findOrFail($id);
        $this->loadAddress();
    }

    protected function loadAddress()
    {
        $nominatim = app(NominatimService::class);
        $result = $nominatim->reverseGeocode($this->area->latitude, $this->area->longitude);
        
        if ($result) {
            $this->address = $result['display_name'];
        }
    }

    public function render()
    {
        return view('livewire.area-management.area-show');
    }
}