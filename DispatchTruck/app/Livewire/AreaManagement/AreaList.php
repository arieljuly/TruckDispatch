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

    public function deleteArea($id)
    {
        $area = Area::findOrFail($id);
        $area->delete();

        session()->flash('message', 'Area deleted successfully!');
        $this->dispatch('areaDeleted');
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

    public function render()
    {
        $areas = Area::query()
            ->when($this->search, function ($query) {
                $query->where('area_name', 'like', '%' . $this->search . '%');
            })
            ->when($this->minLiters, function ($query) {
                $query->where('required_liters', '>=', $this->minLiters);
            })
            ->when($this->maxLiters, function ($query) {
                $query->where('required_liters', '<=', $this->maxLiters);
            })
            ->paginate($this->perPage);

        return view('livewire.area-management.area-list', [
            'areas' => $areas
        ]);
    }
}