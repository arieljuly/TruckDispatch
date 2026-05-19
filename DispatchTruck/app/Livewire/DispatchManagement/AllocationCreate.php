<?php

namespace App\Livewire\DispatchManagement;

use App\Models\Area;
use App\Models\DispatchAllocation;
use App\Models\DispatchSession;
use App\Models\Truck;
use Livewire\Component;

class AllocationCreate extends Component
{
    public $dispatchSessionId;
    public $truckId;
    public $areaId;
    public $litersAllocated;  // Changed from allocatedLiters
    public $distanceUsed;
    public $isPrimaryArea = true;
    public $status = 'pending';
    public $notes = '';

    public $dispatchSessions = [];
    public $trucks = [];
    public $areas = [];

    protected $rules = [
        'dispatchSessionId' => 'required|exists:dispatch_sessions,id',
        'truckId' => 'required|exists:trucks,id',
        'areaId' => 'required|exists:areas,id',
        'litersAllocated' => 'required|numeric|min:0.01',  // Changed
        'distanceUsed' => 'required|numeric|min:0',
        'isPrimaryArea' => 'boolean',
        'status' => 'required|in:pending,completed,failed',
    ];

    protected $messages = [
        'dispatchSessionId.required' => 'Please select a dispatch session',
        'truckId.required' => 'Please select a truck',
        'areaId.required' => 'Please select an area',
        'litersAllocated.required' => 'Please enter allocated liters',  // Changed
        'litersAllocated.min' => 'Allocated liters must be greater than 0',  // Changed
    ];

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $this->dispatchSessions = DispatchSession::where('status', 'executed')
            ->orderBy('created_at', 'desc')
            ->get();

        $this->trucks = Truck::where('status', 'available')
            ->where('deleted_at', null)
            ->get();

        $this->areas = Area::where('status', 'active')->get();
    }

    public function updatedDispatchSessionId($value)
    {
        if ($value) {
            $session = DispatchSession::find($value);
            if ($session && $session->predicted_fuel_liters) {
                $this->litersAllocated = $session->predicted_fuel_liters;  // Changed
                $this->distanceUsed = $session->distance_km;
            }
        }
    }

    public function updatedTruckId($value)
    {
        if ($value) {
            $truck = Truck::find($value);
            if ($truck && $this->litersAllocated > $truck->available_ltrs) {  // Changed
                $this->addError('litersAllocated', "Truck only has {$truck->available_ltrs} L available. Please adjust allocated liters.");  // Changed
            } else {
                $this->resetErrorBag('litersAllocated');  // Changed
            }
        }
    }

    public function save()
    {
        $this->validate();

        // Check truck availability again
        $truck = Truck::find($this->truckId);
        if ($truck && $this->litersAllocated > $truck->available_ltrs) {  // Changed
            $this->addError('litersAllocated', "Insufficient fuel. Truck has {$truck->available_ltrs} L available.");  // Changed
            return;
        }

        // Create allocation
        $allocation = DispatchAllocation::create([
            'dispatch_session_id' => $this->dispatchSessionId,
            'truck_id' => $this->truckId,
            'area_id' => $this->areaId,
            'liters_allocated' => $this->litersAllocated,  // Changed
            'distance_used' => $this->distanceUsed,
            'is_primary_area' => $this->isPrimaryArea,
            'status' => $this->status,
        ]);

        // Update truck available liters
        if ($truck) {
            $truck->available_ltrs -= $this->litersAllocated;  // Changed
            $truck->save();
        }

        session()->flash('message', 'Allocation created successfully!');
        session()->flash('allocation_id', $allocation->id);

        return redirect()->route('dispatch.allocations');
    }

    public function render()
    {
        return view('livewire.dispatch-management.allocation-create', [
            'statuses' => ['pending', 'completed', 'failed'],
        ]);
    }
}