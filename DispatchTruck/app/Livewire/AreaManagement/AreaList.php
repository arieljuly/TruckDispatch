<?php

namespace App\Livewire\AreaManagement;

use App\Models\Area;
use App\Models\Station;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class AreaList extends Component
{
    use WithPagination;

    public $activeTab = 'areas'; // 'areas' or 'stations'

    // Area filters
    public $search = '';
    public $minLiters = '';
    public $maxLiters = '';
    public $perPage = 10;
    public $showInactive = true;

    // Station filters
    public $stationSearch = '';
    public $stationAreaFilter = '';
    public $stationClientFilter = '';
    public $stationPerPage = 10;
    public $stationShowInactive = true;

    public function deleteArea($id)
    {
        // Use withTrashed to find the area even if it's already soft deleted
        $area = Area::withTrashed()->find($id);

        if (!$area) {
            session()->flash('error', 'Area not found!');
            return;
        }

        // Only deactivate if it's currently active
        if ($area->status === 'active') {
            $area->update([
                'status' => 'inactive',
            ]);
            $area->delete();
            session()->flash('message', 'Area deactivated successfully!');
            $this->dispatch('areaDeleted');
        } else {
            session()->flash('error', 'Area is already inactive!');
        }

        $this->resetPage();
    }

    public function reactivateArea($id)
    {
        // Find the area including soft deleted ones
        $area = Area::withTrashed()->find($id);

        if (!$area) {
            session()->flash('error', 'Area not found!');
            return;
        }

        // Only reactivate if it's currently inactive
        if ($area->status === 'inactive') {
            $area->restore();
            $area->update([
                'status' => 'active',
            ]);
            session()->flash('message', 'Area reactivated successfully!');
            $this->dispatch('areaReactivated');
        } else {
            session()->flash('error', 'Area is already active!');
        }

        $this->resetPage();
    }

    public function deleteStation($id)
    {
        // Use withTrashed to find the station even if it's already soft deleted
        $station = Station::withTrashed()->find($id);

        if (!$station) {
            session()->flash('error', 'Station not found!');
            return;
        }

        // Only deactivate if it's currently active
        if ($station->status === 'active') {
            $station->update([
                'status' => 'inactive',
            ]);
            $station->delete();
            session()->flash('message', 'Station deactivated successfully!');
            $this->dispatch('stationDeleted');
        } else {
            session()->flash('error', 'Station is already inactive!');
        }

        $this->resetPage('stationsPage');
    }

    public function reactivateStation($id)
    {
        // Find the station including soft deleted ones
        $station = Station::withTrashed()->find($id);

        if (!$station) {
            session()->flash('error', 'Station not found!');
            return;
        }

        if ($station->status === 'inactive') {
            $station->restore();
            $station->update([
                'status' => 'active',
            ]);
            session()->flash('message', 'Station reactivated successfully!');
            $this->dispatch('stationReactivated');
        } else {
            session()->flash('error', 'Station is already active!');
        }

        $this->resetPage('stationsPage');
    }

    // Area filter methods
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingMinLiters()
    {
        $this->resetPage();
    }

    public function updatingMaxLiters()
    {
        $this->resetPage();
    }

    public function updatingShowInactive()
    {
        $this->resetPage();
    }

    // Station filter methods
    public function updatingStationSearch()
    {
        $this->resetPage('stationsPage');
    }

    public function updatingStationAreaFilter()
    {
        $this->resetPage('stationsPage');
    }

    public function updatingStationClientFilter()
    {
        $this->resetPage('stationsPage');
    }

    public function updatingStationShowInactive()
    {
        $this->resetPage('stationsPage');
    }

    public function getAreasProperty()
    {
        // Use withTrashed() to include soft deleted records
        $query = Area::withTrashed()
            ->withCount('stations')
            ->when($this->search, function ($query) {
                $query->where('area_name', 'like', '%' . $this->search . '%')
                    ->orWhere('area_code', 'like', '%' . $this->search . '%');
            });

        // If not showing inactive, filter by status active
        if (!$this->showInactive) {
            $query->where('status', 'active');
        }

        // Get all results first (we need to sort manually)
        $areas = $query->get();

        // Filter by required_liters if specified
        if ($this->minLiters || $this->maxLiters) {
            $areas = $areas->filter(function ($area) {
                if ($this->minLiters && $area->required_liters < (float) $this->minLiters) {
                    return false;
                }
                if ($this->maxLiters && $area->required_liters > (float) $this->maxLiters) {
                    return false;
                }
                return true;
            });
        }

        // Sort: Active first (by created_at desc), then inactive (by created_at desc)
        $sortedAreas = $areas->sortByDesc(function ($area) {
            // Active items get higher priority (1), inactive get lower priority (0)
            $priority = $area->status === 'active' ? 1 : 0;
            // Combine priority with created_at timestamp for sorting
            return $priority . '_' . $area->created_at->timestamp;
        })->values();

        // Paginate manually
        $currentPage = request()->get('areasPage', 1);
        $pagedData = $sortedAreas->forPage($currentPage, $this->perPage);

        // Create paginator instance
        $areas = new \Illuminate\Pagination\LengthAwarePaginator(
            $pagedData,
            $sortedAreas->count(),
            $this->perPage,
            $currentPage,
            ['path' => request()->url(), 'pageName' => 'areasPage']
        );

        return $areas;
    }

    public function getStationsProperty()
    {
        // Use withTrashed() to include soft deleted records
        $query = Station::withTrashed()
            ->with(['area', 'client'])
            ->when($this->stationSearch, function ($query) {
                $query->where('station_name', 'like', '%' . $this->stationSearch . '%')
                    ->orWhere('station_code', 'like', '%' . $this->stationSearch . '%')
                    ->orWhere('address', 'like', '%' . $this->stationSearch . '%');
            })
            ->when($this->stationAreaFilter, function ($query) {
                $query->where('area_id', $this->stationAreaFilter);
            })
            ->when($this->stationClientFilter, function ($query) {
                $query->where('user_id', $this->stationClientFilter);
            });

        // If not showing inactive, filter by status active
        if (!$this->stationShowInactive) {
            $query->where('status', 'active');
        }

        // Get all results
        $stations = $query->get();

        // Sort: Active first (by created_at desc), then inactive (by created_at desc)
        $sortedStations = $stations->sortByDesc(function ($station) {
            // Active items get higher priority (1), inactive get lower priority (0)
            $priority = $station->status === 'active' ? 1 : 0;
            // Combine priority with created_at timestamp for sorting
            return $priority . '_' . $station->created_at->timestamp;
        })->values();

        // Paginate manually
        $currentPage = request()->get('stationsPage', 1);
        $pagedData = $sortedStations->forPage($currentPage, $this->stationPerPage);

        // Create paginator instance
        $stations = new \Illuminate\Pagination\LengthAwarePaginator(
            $pagedData,
            $sortedStations->count(),
            $this->stationPerPage,
            $currentPage,
            ['path' => request()->url(), 'pageName' => 'stationsPage']
        );

        return $stations;
    }

    public function render()
    {
        $areas = $this->areas;
        $stations = $this->stations;

        // Get data for filters
        $allAreas = Area::orderBy('area_name')->get(['id', 'area_name', 'area_code']);
        $allClients = User::whereHas('role', function ($query) {
            $query->where('role_name', 'client');
        })->orderBy('first_name')->get(['id', 'first_name', 'last_name', 'company_name']);

        return view('livewire.area-management.area-list', [
            'areas' => $areas,
            'stations' => $stations,
            'allAreas' => $allAreas,
            'allClients' => $allClients,
        ]);
    }
}