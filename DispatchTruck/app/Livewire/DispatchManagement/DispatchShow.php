<?php

namespace App\Livewire\DispatchManagement;

use App\Models\DispatchSession;
use App\Services\DispatchManagementService;
use Livewire\Component;

class DispatchShow extends Component
{
    public $sessionId;
    public $session;
    public $allocations;
    public $actualFuelUsed;
    public $showFeedbackForm = false;

    protected $dispatchService;

    public function boot(DispatchManagementService $dispatchService)
    {
        $this->dispatchService = $dispatchService;
    }

    public function mount($id)
    {
        $this->sessionId = $id;
        $this->loadData();
    }

    public function loadData()
    {
        $this->session = DispatchSession::with(['recommendedTruck', 'assignedTruck', 'executor'])
            ->findOrFail($this->sessionId);

        $this->allocations = $this->session->allocations()->with(['truck', 'area'])->get();
    }

    public function recordActualFuel()
    {
        $this->validate([
            'actualFuelUsed' => 'required|numeric|min:0'
        ]);

        $this->dispatchService->recordActualFuelUsage($this->session, $this->actualFuelUsed);

        $this->session->refresh();
        $this->showFeedbackForm = false;

        $this->dispatch('notify', [
            'message' => 'Actual fuel usage recorded successfully',
            'type' => 'success'
        ]);
    }

    public function render()
    {
        $predictionError = null;
        if ($this->session->actual_fuel_used && $this->session->predicted_fuel_liters) {
            $error = abs($this->session->actual_fuel_used - $this->session->predicted_fuel_liters);
            $predictionError = round(($error / $this->session->predicted_fuel_liters) * 100, 2);
        }

        return view('livewire.dispatch-management.dispatch-show', [
            'predictionError' => $predictionError
        ]);
    }
}