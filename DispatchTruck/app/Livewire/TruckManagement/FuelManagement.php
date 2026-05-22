<?php

namespace App\Livewire\TruckManagement;

use App\Models\FuelType;
use Livewire\Component;
use Livewire\WithPagination;

class FuelManagement extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $perPage = 10;

    protected $queryString = ['search', 'statusFilter', 'perPage'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->statusFilter = '';
        $this->resetPage();
    }

    public function restoreFuel($id)
    {
        $fuel = FuelType::withTrashed()->findOrFail($id);
        $fuel->restore();
        $fuel->update(['status' => 'active']);

        session()->flash('message', 'Fuel type restored successfully.');
        session()->flash('message_type', 'success');
    }

    public function deleteFuel($id)
    {
        $fuel = FuelType::findOrFail($id);
        $fuel->delete(); // This sets deleted_at to now via SoftDeletes
        $fuel->update(['status' => 'inactive']);

        session()->flash('message', 'Fuel type deleted successfully.');
        session()->flash('message_type', 'success');
    }

    public function forceDeleteFuel($id)
    {
        $fuel = FuelType::withTrashed()->findOrFail($id);
        $fuel->forceDelete();

        session()->flash('message', 'Fuel type permanently deleted.');
        session()->flash('message_type', 'success');
    }

    public function render()
    {
        // Get total count including trashed
        $totalFuels = FuelType::withTrashed()->count();

        // Build query with filters
        $fuels = FuelType::withTrashed()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('fuel_code', 'like', '%' . $this->search . '%')
                        ->orWhere('fuel_name', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->statusFilter == 'deleted', function ($query) {
                $query->whereNotNull('deleted_at');
            })
            ->when($this->statusFilter == 'active', function ($query) {
                $query->where('status', 'active')->whereNull('deleted_at');
            })
            ->when($this->statusFilter == 'inactive', function ($query) {
                $query->where('status', 'inactive')->whereNull('deleted_at');
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        return view('livewire.truck-management.fuel-management', [
            'fuels' => $fuels,
            'totalFuels' => $totalFuels,
        ]);
    }
}