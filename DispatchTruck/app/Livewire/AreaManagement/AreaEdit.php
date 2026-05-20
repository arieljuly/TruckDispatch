<?php

namespace App\Livewire\AreaManagement;

use App\Models\Area;
use App\Models\Station;
use App\Models\User;
use App\Services\NominatimService;
use Livewire\Component;
use Livewire\Attributes\Rule;

class AreaEdit extends Component
{
    // Type selection
    public $location_type = 'area'; // 'area' or 'station'
    public $item_id; // ID of area or station being edited

    // Common fields
    #[Rule('required|string|max:255')]
    public $name = '';

    #[Rule('required|numeric|between:-90,90')]
    public $latitude = '';

    #[Rule('required|numeric|between:-180,180')]
    public $longitude = '';

    // Area specific fields
    #[Rule('required|string|max:50')]
    public $area_code = '';

    // Station specific fields
    public $area_id = '';
    public $user_id = '';
    #[Rule('required|numeric|min:0')]
    public $required_liters = '';
    public $address = '';
    #[Rule('required|string|max:50')]
    public $station_code = '';

    // Search related
    public $search_query = '';
    public $search_results = [];
    public $show_search = false;
    public $selected_address = '';

    // Dropdown data
    public $areas = [];
    public $clients = [];

    protected $nominatim;

    public function boot(NominatimService $nominatim)
    {
        $this->nominatim = $nominatim;
    }

    public function mount($id = null)
    {
        // Get the type from the request query parameter
        $type = request()->query('type', 'area');

        $this->location_type = $type;
        $this->item_id = $id;

        $this->loadAreas();
        $this->loadClients();

        if ($type === 'area') {
            $this->loadAreaData($id);
        } else {
            $this->loadStationData($id);
        }
    }

    public function loadAreaData($id)
    {
        $area = Area::findOrFail($id);
        $this->name = $area->area_name;
        $this->area_code = $area->area_code;
        $this->latitude = $area->latitude;
        $this->longitude = $area->longitude;

        $this->loadAddressFromCoordinates();
    }

    public function loadStationData($id)
    {
        $station = Station::with(['area', 'client'])->findOrFail($id);
        $this->name = $station->station_name;
        $this->station_code = $station->station_code;
        $this->area_id = $station->area_id;
        $this->user_id = $station->user_id;
        $this->required_liters = $station->required_liters;
        $this->latitude = $station->latitude;
        $this->longitude = $station->longitude;
        $this->address = $station->address;
        $this->selected_address = $station->address;

        $this->loadAddressFromCoordinates();
    }

    public function updatedLocationType()
    {
        $this->resetValidation();
        $this->selected_address = '';
        $this->search_query = '';
        $this->search_results = [];
        $this->show_search = false;
        $this->name = '';
        $this->area_code = '';
        $this->station_code = '';

        if ($this->location_type === 'station') {
            $this->loadAreas();
            $this->loadClients();
        }
    }

    public function loadAreas()
    {
        $this->areas = Area::orderBy('area_name')->get(['id', 'area_name', 'area_code']);
    }

    public function loadClients()
    {
        $this->clients = User::whereHas('role', function ($query) {
            $query->where('role_name', 'client');
        })->orderBy('first_name')->get(['id', 'first_name', 'last_name', 'company_name']);
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

        if (empty($this->name)) {
            $this->name = $this->extractCityName($displayName);
        }

        if ($this->location_type === 'station') {
            $this->address = $displayName;
        }

        $this->dispatch('updateMap', lat: (float) $lat, lon: (float) $lon, zoom: 15);
    }

    public function updatedLatitude($value)
    {
        if (!empty($value) && !empty($this->longitude)) {
            $this->dispatch('updateMap', lat: (float) $value, lon: (float) $this->longitude, zoom: 15);
            $this->reverseGeocode();
        }
    }

    public function updatedLongitude($value)
    {
        if (!empty($value) && !empty($this->latitude)) {
            $this->dispatch('updateMap', lat: (float) $this->latitude, lon: (float) $value, zoom: 15);
            $this->reverseGeocode();
        }
    }

    public function reverseGeocode()
    {
        if (!empty($this->latitude) && !empty($this->longitude)) {
            $address = $this->nominatim->reverseGeocode($this->latitude, $this->longitude);
            if ($address) {
                $this->selected_address = $address['display_name'];
                if ($this->location_type === 'station') {
                    $this->address = $address['display_name'];
                }
            }
        }
    }

    public function loadAddressFromCoordinates()
    {
        if (!empty($this->latitude) && !empty($this->longitude)) {
            $address = $this->nominatim->reverseGeocode($this->latitude, $this->longitude);
            if ($address) {
                $this->selected_address = $address['display_name'];
                if ($this->location_type === 'station' && empty($this->address)) {
                    $this->address = $address['display_name'];
                }
            }
        }
    }

    protected function extractCityName($displayName)
    {
        $parts = explode(',', $displayName);
        return trim($parts[0]);
    }

    public function update()
    {
        if ($this->location_type === 'area') {
            // Validate for Area
            $this->validate([
                'name' => 'required|string|max:255',
                'area_code' => 'required|string|max:50|unique:areas,area_code,' . $this->item_id,
                'latitude' => 'required|numeric|between:-90,90',
                'longitude' => 'required|numeric|between:-180,180',
            ]);

            $area = Area::findOrFail($this->item_id);
            $area->update([
                'area_name' => $this->name,
                'area_code' => $this->area_code,
                'latitude' => $this->latitude,
                'longitude' => $this->longitude,
            ]);

            session()->flash('message', 'Area updated successfully!');
        } else {
            // Validate for Station
            $this->validate([
                'name' => 'required|string|max:255',
                'station_code' => 'required|string|max:50|unique:stations,station_code,' . $this->item_id,
                'area_id' => 'required|exists:areas,id',
                'user_id' => 'required|exists:users,id',
                'required_liters' => 'required|numeric|min:0',
                'latitude' => 'required|numeric|between:-90,90',
                'longitude' => 'required|numeric|between:-180,180',
                'address' => 'nullable|string|max:500',
            ]);

            $station = Station::findOrFail($this->item_id);
            $station->update([
                'station_name' => $this->name,
                'station_code' => $this->station_code,
                'area_id' => $this->area_id,
                'user_id' => $this->user_id,
                'required_liters' => $this->required_liters,
                'latitude' => $this->latitude,
                'longitude' => $this->longitude,
                'address' => $this->address ?: $this->selected_address,
            ]);

            session()->flash('message', 'Station updated successfully!');
        }

        return redirect()->route('admin.areas.index');
    }

    public function render()
    {
        return view('livewire.area-management.area-edit');
    }
}