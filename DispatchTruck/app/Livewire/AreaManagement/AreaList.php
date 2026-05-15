<?php

namespace App\Livewire\AreaManagement;

use App\Models\Area;
use Livewire\Component;
use Livewire\WithPagination;

class AreaList extends Component
{
    use WithPagination;

    public $search = '';
    public $minLiters = '';
    public $maxLiters = '';
    public $perPage = 10;
    public $showInactive = true; // Show both active and inactive by default

    public function deleteArea($id)
    {
        $area = Area::findOrFail($id);

        // Update status to inactive and set deleted_at timestamp
        $area->update([
            'status' => 'inactive',
            'deleted_at' => now()
        ]);

        session()->flash('message', 'Area deactivated successfully!');
        $this->dispatch('areaDeleted');
    }

    public function reactivateArea($id)
    {
        $area = Area::findOrFail($id);

        // Update status to active and set deleted_at to null
        $area->update([
            'status' => 'active',
            'deleted_at' => null
        ]);

        session()->flash('message', 'Area reactivated successfully!');
        $this->dispatch('areaReactivated');
    }

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

    public function render()
    {
        $areas = Area::query()
            ->when(!$this->showInactive, function ($query) {
                $query->where('status', 'active');
            })
            ->when($this->search, function ($query) {
                $query->where('area_name', 'like', '%' . $this->search . '%');
            })
            ->when($this->minLiters, function ($query) {
                $query->where('required_liters', '>=', $this->minLiters);
            })
            ->when($this->maxLiters, function ($query) {
                $query->where('required_liters', '<=', $this->maxLiters);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        return view('livewire.area-management.area-list', [
            'areas' => $areas
        ]);
    }
}