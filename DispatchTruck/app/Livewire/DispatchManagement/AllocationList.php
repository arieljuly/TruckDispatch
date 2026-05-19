<?php

namespace App\Livewire\DispatchManagement;

use App\Models\DispatchAllocation;
use Livewire\Component;
use Livewire\WithPagination;

class AllocationList extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $sessionId = null;
    public $perPage = 10;

    protected $queryString = ['search', 'statusFilter', 'sessionId'];

    public function getSummaryStatsProperty()
    {
        return [
            'total_allocations' => DispatchAllocation::count(),
            'pending_allocations' => DispatchAllocation::where('status', 'pending')->count(),
            'completed_allocations' => DispatchAllocation::where('status', 'completed')->count(),
            'failed_allocations' => DispatchAllocation::where('status', 'failed')->count(),
            'total_fuel_allocated' => DispatchAllocation::sum('liters_allocated'),
        ];
    }

    public function render()
    {
        $allocations = DispatchAllocation::query()
            ->with(['truck', 'area', 'dispatchSession'])
            ->when($this->sessionId, function ($query) {
                $query->where('dispatch_session_id', $this->sessionId);
            })
            ->when($this->search, function ($query) {
                $query->whereHas('truck', function ($q) {
                    $q->where('truck_name', 'like', '%' . $this->search . '%')
                        ->orWhere('plate_number', 'like', '%' . $this->search . '%');
                })->orWhereHas('area', function ($q) {
                    $q->where('area_name', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.dispatch-management.allocation-list', [
            'allocations' => $allocations,
            'statuses' => ['pending', 'completed', 'failed']
        ]);
    }

    public function updateStatus($allocationId, $status)
    {
        $allocation = DispatchAllocation::findOrFail($allocationId);
        $allocation->update(['status' => $status]);

        session()->flash('message', "Allocation status updated to {$status}");
    }

    public function deleteAllocation($allocationId)
    {
        $allocation = DispatchAllocation::findOrFail($allocationId);

        if (in_array($allocation->status, ['pending', 'completed'])) {
            $truck = $allocation->truck;
            if ($truck) {
                $truck->available_ltrs += $allocation->liters_allocated; // Changed from allocated_liters
                $truck->save();
            }
        }

        $allocation->delete();

        session()->flash('message', 'Allocation deleted successfully');
    }
}