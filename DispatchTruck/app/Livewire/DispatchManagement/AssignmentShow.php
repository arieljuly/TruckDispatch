<?php

namespace App\Livewire\DispatchManagement;

use App\Models\TruckAssignment;
use Livewire\Component;

class AssignmentShow extends Component
{
    public $assignmentId;
    public $assignment;

    public function mount($id)
    {
        $this->assignmentId = $id;
        $this->loadAssignment();
    }

    public function loadAssignment()
    {
        $this->assignment = TruckAssignment::with(['truck', 'driver.user'])
            ->findOrFail($this->assignmentId);
    }

    public function completeAssignment()
    {
        $this->assignment->update([
            'status' => 'completed',
            'end_time' => now()
        ]);

        $this->loadAssignment();

        $this->dispatch('notify', [
            'message' => 'Assignment marked as completed',
            'type' => 'success'
        ]);
    }

    public function render()
    {
        return view('livewire.dispatch-management.assignment-show');
    }
}