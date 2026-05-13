<?php

namespace App\Livewire\AreaManagement;

use App\Models\Area;
use App\Services\NominatimService;
use Livewire\Component;
use Livewire\Attributes\Rule;

class AreaEdit extends Component
{
    public $area_id;

    #[Rule('required|string|max:255')]
    public $area_name = '';

    #[Rule('required|numeric|min:0')]
    public $required_liters = '';

    #[Rule('required|numeric|between:-90,90')]
    public $latitude = '';

    #[Rule('required|numeric|between:-180,180')]
    public $longitude = '';

    public $search_query = '';
    public $search_results = [];
    public $show_search = false;
    public $selected_address = '';

    protected $nominatim;

    public function boot(NominatimService $nominatim)
    {
        $this->nominatim = $nominatim;
    }

    public function mount($id = null)
    {
        if ($id) {
            $area = Area::findOrFail($id);
            $this->area_id = $area->id;
            $this->area_name = $area->area_name;
            $this->required_liters = $area->required_liters;
            $this->latitude = $area->latitude;
            $this->longitude = $area->longitude;

            $this->loadAddressFromCoordinates();
        }
    }

    public function searchLocation()
    {
        if (strlen($this->search_query) < 3) {
            return;
        }

        $this->search_results = $this->nominatim->search($this->search_query);
        $this->show_search = true;
    }

    public function selectLocation($lat, $lon, $displayName)
    {
        $this->latitude = $lat;
        $this->longitude = $lon;
        $this->selected_address = $displayName;
        $this->search_results = [];
        $this->show_search = false;
        $this->search_query = '';

        $this->dispatch('updateMap', lat: (float) $lat, lon: (float) $lon, zoom: 15);
    }

    public function updatedLatitude($value)
    {
        if (!empty($value) && !empty($this->longitude)) {
            $this->dispatch('updateMap', lat: (float) $value, lon: (float) $this->longitude, zoom: 15);
        }
    }

    public function updatedLongitude($value)
    {
        if (!empty($value) && !empty($this->latitude)) {
            $this->dispatch('updateMap', lat: (float) $this->latitude, lon: (float) $value, zoom: 15);
        }
    }

    public function loadAddressFromCoordinates()
    {
        if (!empty($this->latitude) && !empty($this->longitude)) {
            $address = $this->nominatim->reverseGeocode($this->latitude, $this->longitude);
            if ($address) {
                $this->selected_address = $address['display_name'];
            }
        }
    }

    public function update()
    {
        $this->validate();

        $area = Area::findOrFail($this->area_id);
        $area->update([
            'area_name' => $this->area_name,
            'required_liters' => $this->required_liters,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
        ]);

        session()->flash('message', 'Area updated successfully!');

        return redirect()->route('admin.areas.index');
    }

    public function render()
    {
        return view('livewire.area-management.area-edit');
    }
}