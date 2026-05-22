<?php

namespace App\Livewire\TruckManagement;

use App\Models\FuelType;
use Livewire\Component;
use Livewire\WithPagination;

class FuelList extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $perPage = 10;
    public $viewingFuel = false;
    public $selectedFuel = null;
    public $viewId = null;

    protected $queryString = ['search', 'statusFilter', 'viewId'];

    public function mount($view = null)
    {
        if ($view) {
            $this->viewId = $view;
            $this->viewFuel($view);
        } elseif (request()->get('view')) {
            $this->viewId = request()->get('view');
            $this->viewFuel(request()->get('view'));
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function viewFuel($id)
    {
        $this->selectedFuel = FuelType::withTrashed()->findOrFail($id);
        $this->viewingFuel = true;
        $this->viewId = $id;
    }

    public function backToList()
    {
        $this->viewingFuel = false;
        $this->selectedFuel = null;
        $this->viewId = null;
        $this->resetPage();
    }

    public function deleteFuel($id)
    {
        $fuel = FuelType::findOrFail($id);
        $fuel->delete();
        $fuel->update(['status' => 'inactive']);

        session()->flash('message', 'Fuel type deleted successfully.');
        session()->flash('message_type', 'success');

        $this->resetPage();
    }

    public function restoreFuel($id)
    {
        $fuel = FuelType::withTrashed()->findOrFail($id);
        $fuel->restore();
        $fuel->update(['status' => 'active']);

        session()->flash('message', 'Fuel type restored successfully.');
        session()->flash('message_type', 'success');

        if ($this->viewingFuel && $this->selectedFuel && $this->selectedFuel->id == $id) {
            $this->viewFuel($id);
        }

        $this->resetPage();
    }

    public function forceDeleteFuel($id)
    {
        $fuel = FuelType::withTrashed()->findOrFail($id);
        $fuel->forceDelete();

        session()->flash('message', 'Fuel type permanently deleted.');
        session()->flash('message_type', 'success');

        if ($this->viewingFuel && $this->selectedFuel && $this->selectedFuel->id == $id) {
            $this->backToList();
        }

        $this->resetPage();
    }

    public function render()
    {
        $fuels = FuelType::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('fuel_code', 'like', '%' . $this->search . '%')
                        ->orWhere('fuel_name', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->orderBy('fuel_code')
            ->paginate($this->perPage);

        return view('livewire.truck-management.fuel-list', [
            'fuels' => $fuels,
        ]);
    }
}