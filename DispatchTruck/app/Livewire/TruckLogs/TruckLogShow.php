<?php

namespace App\Livewire\TruckLogs;

use App\Models\Truck;
use App\Models\TruckLog;
use Livewire\Component;
use Livewire\WithPagination;

class TruckLogShow extends Component
{
    use WithPagination;

    public $search = '';
    public $actionFilter = '';
    public $truckFilter = '';
    public $dateFrom = '';
    public $dateTo = '';
    public $perPage = 15;

    protected $listeners = ['refreshLogs' => 'refreshLogs'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->actionFilter = '';
        $this->truckFilter = '';
        $this->dateFrom = '';
        $this->dateTo = '';
        $this->resetPage();
    }

    // Add the missing refreshLogs method
    public function refreshLogs()
    {
        // This method just triggers a refresh of the component
        // No need to do anything else as the render will be called automatically
        $this->dispatch('$refresh');
    }

    public function render()
    {
        $logs = TruckLog::query()
            ->with([
                'truck' => function ($query) {
                    $query->withTrashed(); // Include soft deleted trucks
                }
            ])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->whereHas('truck', function ($sub) {
                        $sub->where('truck_name', 'like', '%' . $this->search . '%')
                            ->orWhere('plate_number', 'like', '%' . $this->search . '%');
                    })
                        ->orWhere('location', 'like', '%' . $this->search . '%')
                        ->orWhere('remarks', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->actionFilter, function ($query) {
                $query->where('action', $this->actionFilter);
            })
            ->when($this->truckFilter, function ($query) {
                $query->where('truck_id', $this->truckFilter);
            })
            ->when($this->dateFrom, function ($query) {
                $query->whereDate('created_at', '>=', $this->dateFrom);
            })
            ->when($this->dateTo, function ($query) {
                $query->whereDate('created_at', '<=', $this->dateTo);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        // Get all trucks including soft deleted ones for the filter dropdown
        $trucks = Truck::withTrashed()->orderBy('truck_name')->get();

        $actionStats = [
            'total' => TruckLog::count(),
            'assigned' => TruckLog::where('action', 'driver_assigned')->count(),
            'delivered' => TruckLog::where('action', 'delivered')->count(),
            'maintenance' => TruckLog::where('action', 'maintenance')->count(),
        ];

        return view('livewire.truck-logs.truck-log-show', [
            'logs' => $logs,
            'trucks' => $trucks,
            'actionStats' => $actionStats,
        ]);
    }
}