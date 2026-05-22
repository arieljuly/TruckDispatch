<?php

namespace App\Livewire\TruckManagement;

use App\Models\Truck;
use App\Models\Area;
use App\Models\TruckLog;
use App\Models\TruckCompartment;
use App\Models\FuelType;
use Livewire\Component;

class TruckCreate extends Component
{
    public $truck_name = '';
    public $plate_number = '';
    public $max_capacity_ltrs = '';
    public $current_area_id = '';
    public $status = 'available';
    public $areas = [];
    public $fuelTypes = [];
    public $compartments = [];

    protected $rules = [
        'truck_name' => 'required|string|max:255',
        'plate_number' => 'required|string|max:50|unique:trucks,plate_number',
        'max_capacity_ltrs' => 'required|numeric|min:0.001',
        'current_area_id' => 'nullable|exists:areas,id',
        'status' => 'required|in:available,in_transit,maintenance',
        'compartments' => 'required|array|min:1',
        'compartments.*.compartment_no' => 'required|string|max:50',
        'compartments.*.current_fuel_type_id' => 'required|exists:fuel_types,id',
        'compartments.*.capacity_ltrs' => 'required|numeric|min:0.001',
        'compartments.*.loaded_ltrs' => 'required|numeric|min:0',
    ];

    protected $messages = [
        'max_capacity_ltrs.required' => 'Maximum capacity is required.',
        'max_capacity_ltrs.min' => 'Maximum capacity must be greater than 0.',
        'plate_number.unique' => 'This plate number is already registered.',
        'compartments.required' => 'At least one compartment is required.',
        'compartments.min' => 'At least one compartment is required.',
        'compartments.*.compartment_no.required' => 'Compartment number is required.',
        'compartments.*.current_fuel_type_id.required' => 'Fuel type is required.',
        'compartments.*.capacity_ltrs.required' => 'Capacity is required.',
        'compartments.*.capacity_ltrs.min' => 'Capacity must be greater than 0.',
        'compartments.*.loaded_ltrs.min' => 'Loaded liters must be a positive number.',
    ];

    private function logTruckActivity($truckId, $action, $liters = null, $location = null, $remarks = null)
    {
        try {
            return TruckLog::create([
                'truck_id' => $truckId,
                'action' => $action,
                'liters' => $liters,
                'location' => $location,
                'remarks' => $remarks,
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to create truck log', [
                'truck_id' => $truckId,
                'action' => $action,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    public function mount()
    {
        $this->areas = Area::where('status', 'active')->orderBy('area_name')->get();
        $this->fuelTypes = FuelType::where('status', 'active')->orderBy('fuel_name')->get();
        $this->addCompartment();
    }

    public function addCompartment()
    {
        $this->compartments[] = [
            'compartment_no' => '',
            'current_fuel_type_id' => '',
            'capacity_ltrs' => '',
            'loaded_ltrs' => 0,
            'available_ltrs' => 0
        ];
    }

    public function removeCompartment($index)
    {
        unset($this->compartments[$index]);
        $this->compartments = array_values($this->compartments);
        $this->validateTotalCapacity();
    }

    public function updatedMaxCapacityLtrs($value)
    {
        $this->validateTotalCapacity();
    }

    public function updatedCompartments($value, $key)
    {
        // Parse the key to get index and field
        if (str_contains($key, '.capacity_ltrs')) {
            $index = explode('.', $key)[0];
            $capacity = floatval($this->compartments[$index]['capacity_ltrs'] ?? 0);
            $loaded = floatval($this->compartments[$index]['loaded_ltrs'] ?? 0);

            // Update available liters
            $this->compartments[$index]['available_ltrs'] = max(0, $capacity - $loaded);

            // Validate loaded doesn't exceed capacity
            if ($loaded > $capacity) {
                $this->addError("compartments.{$index}.loaded_ltrs", 'Loaded liters cannot exceed capacity.');
            } else {
                $this->resetErrorBag("compartments.{$index}.loaded_ltrs");
            }

            // Validate total capacity doesn't exceed max truck capacity
            $this->validateTotalCapacity();
        }

        if (str_contains($key, '.loaded_ltrs')) {
            $index = explode('.', $key)[0];
            $capacity = floatval($this->compartments[$index]['capacity_ltrs'] ?? 0);
            $loaded = floatval($value);

            // Update available liters
            $this->compartments[$index]['available_ltrs'] = max(0, $capacity - $loaded);

            // Validate loaded doesn't exceed capacity
            if ($loaded > $capacity) {
                $this->addError("compartments.{$index}.loaded_ltrs", 'Loaded liters cannot exceed capacity.');
            } else {
                $this->resetErrorBag("compartments.{$index}.loaded_ltrs");
            }
        }
    }

    private function validateTotalCapacity()
    {
        if (empty($this->max_capacity_ltrs)) {
            return true;
        }

        $totalCompartmentCapacity = 0;
        foreach ($this->compartments as $compartment) {
            $totalCompartmentCapacity += floatval($compartment['capacity_ltrs'] ?? 0);
        }

        $maxCapacity = floatval($this->max_capacity_ltrs);

        if ($totalCompartmentCapacity > $maxCapacity) {
            $this->addError('max_capacity_ltrs', "Total compartment capacity ({$totalCompartmentCapacity}L) exceeds truck's maximum capacity ({$maxCapacity}L).");
            return false;
        } elseif ($totalCompartmentCapacity > 0) {
            $this->resetErrorBag('max_capacity_ltrs');
        }

        return true;
    }

    public function calculateRemainingCapacity()
    {
        if (empty($this->max_capacity_ltrs)) {
            return 0;
        }

        $totalCompartmentCapacity = 0;
        foreach ($this->compartments as $compartment) {
            $totalCompartmentCapacity += floatval($compartment['capacity_ltrs'] ?? 0);
        }

        return max(0, floatval($this->max_capacity_ltrs) - $totalCompartmentCapacity);
    }

    public function calculateTotalCompartmentCapacity()
    {
        $total = 0;
        foreach ($this->compartments as $compartment) {
            $total += floatval($compartment['capacity_ltrs'] ?? 0);
        }
        return $total;
    }

    public function createTruck()
    {
        // Validate total capacity doesn't exceed max
        if (!$this->validateTotalCapacity()) {
            return;
        }

        // Validate compartments
        foreach ($this->compartments as $index => $compartment) {
            if (floatval($compartment['loaded_ltrs'] ?? 0) > floatval($compartment['capacity_ltrs'] ?? 0)) {
                $this->addError("compartments.{$index}.loaded_ltrs", 'Loaded liters cannot exceed capacity.');
                return;
            }
        }

        $this->validate();

        // Create the truck
        $truck = Truck::create([
            'truck_name' => $this->truck_name,
            'plate_number' => $this->plate_number,
            'max_capacity_ltrs' => floatval($this->max_capacity_ltrs),
            'current_area_id' => $this->current_area_id ?: null,
            'status' => $this->status,
        ]);

        // Create compartments
        foreach ($this->compartments as $compartment) {
            TruckCompartment::create([
                'truck_id' => $truck->id,
                'compartment_no' => $compartment['compartment_no'],
                'current_fuel_type_id' => $compartment['current_fuel_type_id'],
                'capacity_ltrs' => floatval($compartment['capacity_ltrs']),
                'loaded_ltrs' => floatval($compartment['loaded_ltrs']),
                'available_ltrs' => floatval($compartment['capacity_ltrs']) - floatval($compartment['loaded_ltrs']),
            ]);
        }

        // Log truck creation
        $areaName = $this->current_area_id ? Area::find($this->current_area_id)->area_name : 'Not assigned';

        $totalCapacity = $truck->compartments()->sum('capacity_ltrs');
        $totalLoaded = $truck->compartments()->sum('loaded_ltrs');

        $remarks = sprintf(
            "New truck registered: %s | Plate: %s | Max Capacity: %.2fL | Total Compartment Capacity: %.2fL | Current Fuel: %.2fL | Compartments: %d | Status: %s",
            $this->truck_name,
            $this->plate_number,
            $truck->max_capacity_ltrs,
            $totalCapacity,
            $totalLoaded,
            count($this->compartments),
            $this->status
        );

        $this->logTruckActivity(
            $truck->id,
            'created',
            $totalCapacity,
            $areaName,
            $remarks
        );

        session()->flash('message', 'Truck created successfully with ' . count($this->compartments) . ' compartment(s)!');
        return redirect()->route('admin.trucks.index');
    }

    public function render()
    {
        return view('livewire.truck-management.truck-create', [
            'remainingCapacity' => $this->calculateRemainingCapacity(),
            'totalCompartmentCapacity' => $this->calculateTotalCompartmentCapacity(),
        ]);
    }
}