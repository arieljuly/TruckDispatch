<?php

namespace App\Livewire\DispatchManagement;

use App\Models\TruckAssignment;
use Livewire\Component;
use Livewire\WithPagination;

class AssignmentList extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $perPage = 10;

    protected $queryString = ['search', 'statusFilter'];

    public function render()
    {
        $assignments = TruckAssignment::query()
            ->with(['truck', 'driver'])
            ->when($this->search, function ($query) {
                $query->whereHas('truck', function ($q) {
                    $q->where('truck_name', 'like', '%' . $this->search . '%');
                })->orWhereHas('driver', function ($q) {
                    $q->whereHas('user', function ($u) {
                        $u->where('first_name', 'like', '%' . $this->search . '%')
                          ->orWhere('last_name', 'like', '%' . $this->search . '%');
                    });
                });
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.dispatch-management.assignment-list', [
            'assignments' => $assignments,
            'statuses' => ['active', 'completed', 'cancelled']
        ]);
    }

    public function cancelAssignment($id)
    {
        $assignment = TruckAssignment::findOrFail($id);
        $assignment->update(['status' => 'cancelled', 'end_time' => now()]);
        
        $this->dispatch('notify', [
            'message' => 'Assignment cancelled successfully',
            'type' => 'success'
        ]);
    }
}