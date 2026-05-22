<?php

namespace App\Livewire\TruckManagement;

use App\Models\Truck;
use App\Models\Area;
use App\Models\TruckLog;
use App\Models\TruckCompartment;
use App\Models\FuelType;
use Livewire\Component;

class TruckEdit extends Component
{
    public $truck;
    public $truck_id;
    public $truck_name = '';
    public $plate_number = '';
    public $max_capacity_ltrs = '';
    public $current_area_id = '';
    public $status = '';
    public $areas = [];
    public $fuelTypes = [];
    public $compartments = [];

    protected $rules = [
        'truck_name' => 'required|string|max:255',
        'plate_number' => 'required|string|max:50',
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

    public function mount($id)
    {
        $this->truck = Truck::with(['compartments.fuelType'])->findOrFail($id);
        $this->truck_id = $this->truck->id;

        $this->truck_name = $this->truck->truck_name;
        $this->plate_number = $this->truck->plate_number;
        $this->max_capacity_ltrs = $this->truck->max_capacity_ltrs;
        $this->current_area_id = $this->truck->current_area_id;
        $this->status = $this->truck->status;

        $this->areas = Area::where('status', 'active')->orderBy('area_name')->get();
        $this->fuelTypes = FuelType::where('status', 'active')->orderBy('fuel_name')->get();

        foreach ($this->truck->compartments as $compartment) {
            $this->compartments[] = [
                'id' => $compartment->id,
                'compartment_no' => $compartment->compartment_no,
                'current_fuel_type_id' => $compartment->current_fuel_type_id,
                'capacity_ltrs' => $compartment->capacity_ltrs,
                'loaded_ltrs' => $compartment->loaded_ltrs,
                'available_ltrs' => $compartment->available_ltrs,
            ];
        }
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
        if (isset($this->compartments[$index]['id'])) {
            $compartmentId = $this->compartments[$index]['id'];
            $compartment = TruckCompartment::find($compartmentId);
            if ($compartment) {
                $compartment->delete();
                $this->logTruckActivity(
                    $this->truck_id,
                    'maintenance',
                    null,
                    $this->truck->currentArea->area_name ?? 'Unknown',
                    "Removed compartment: {$compartment->compartment_no}"
                );
            }
        }

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
        if (str_contains($key, '.capacity_ltrs')) {
            $index = explode('.', $key)[0];
            $capacity = floatval($this->compartments[$index]['capacity_ltrs'] ?? 0);
            $loaded = floatval($this->compartments[$index]['loaded_ltrs'] ?? 0);

            $this->compartments[$index]['available_ltrs'] = max(0, $capacity - $loaded);

            if ($loaded > $capacity) {
                $this->addError("compartments.{$index}.loaded_ltrs", 'Loaded liters cannot exceed capacity.');
            } else {
                $this->resetErrorBag("compartments.{$index}.loaded_ltrs");
            }

            $this->validateTotalCapacity();
        }

        if (str_contains($key, '.loaded_ltrs')) {
            $index = explode('.', $key)[0];
            $capacity = floatval($this->compartments[$index]['capacity_ltrs'] ?? 0);
            $loaded = floatval($value);

            $this->compartments[$index]['available_ltrs'] = max(0, $capacity - $loaded);

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
        }

        $this->resetErrorBag('max_capacity_ltrs');
        return true;
    }

    public function getRemainingCapacityProperty()
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

    public function getTotalCompartmentCapacityProperty()
    {
        $total = 0;
        foreach ($this->compartments as $compartment) {
            $total += floatval($compartment['capacity_ltrs'] ?? 0);
        }
        return $total;
    }

    public function updateTruck()
    {
        if (!$this->validateTotalCapacity()) {
            return;
        }

        $this->rules['plate_number'] = 'required|string|max:50|unique:trucks,plate_number,' . $this->truck_id;

        foreach ($this->compartments as $index => $compartment) {
            if (floatval($compartment['loaded_ltrs'] ?? 0) > floatval($compartment['capacity_ltrs'] ?? 0)) {
                $this->addError("compartments.{$index}.loaded_ltrs", 'Loaded liters cannot exceed capacity.');
                return;
            }
        }

        $this->validate();

        $truck = Truck::findOrFail($this->truck_id);

        $changes = [];
        if ($truck->truck_name != $this->truck_name) {
            $changes[] = "Name: {$truck->truck_name} → {$this->truck_name}";
        }
        if ($truck->plate_number != $this->plate_number) {
            $changes[] = "Plate: {$truck->plate_number} → {$this->plate_number}";
        }
        if ($truck->max_capacity_ltrs != $this->max_capacity_ltrs) {
            $changes[] = "Max Capacity: {$truck->max_capacity_ltrs}L → {$this->max_capacity_ltrs}L";
        }
        if ($truck->current_area_id != $this->current_area_id) {
            $oldArea = $truck->current_area_id ? Area::find($truck->current_area_id)->area_name : 'Not assigned';
            $newArea = $this->current_area_id ? Area::find($this->current_area_id)->area_name : 'Not assigned';
            $changes[] = "Area: {$oldArea} → {$newArea}";
        }
        if ($truck->status != $this->status) {
            $changes[] = "Status: {$truck->status} → {$this->status}";
        }

        $truck->update([
            'truck_name' => $this->truck_name,
            'plate_number' => $this->plate_number,
            'max_capacity_ltrs' => floatval($this->max_capacity_ltrs),
            'current_area_id' => $this->current_area_id ?: null,
            'status' => $this->status,
        ]);

        if (!empty($changes)) {
            $areaName = $truck->currentArea ? $truck->currentArea->area_name : 'Unknown location';
            $this->logTruckActivity(
                $this->truck_id,
                'status_change',
                null,
                $areaName,
                "Truck updated: " . implode(' | ', $changes)
            );
        }

        // Get IDs of compartments that still exist
        $existingCompartmentIds = [];

        // Update or create compartments
        foreach ($this->compartments as $compartmentData) {
            if (isset($compartmentData['id'])) {
                $existingCompartmentIds[] = $compartmentData['id'];
                $compartment = TruckCompartment::find($compartmentData['id']);
                if ($compartment) {
                    $oldCapacity = $compartment->capacity_ltrs;
                    $oldLoaded = $compartment->loaded_ltrs;

                    $compartment->update([
                        'compartment_no' => $compartmentData['compartment_no'],
                        'current_fuel_type_id' => $compartmentData['current_fuel_type_id'],
                        'capacity_ltrs' => floatval($compartmentData['capacity_ltrs']),
                        'loaded_ltrs' => floatval($compartmentData['loaded_ltrs']),
                        'available_ltrs' => floatval($compartmentData['capacity_ltrs']) - floatval($compartmentData['loaded_ltrs']),
                    ]);

                    if ($oldCapacity != $compartmentData['capacity_ltrs']) {
                        $this->logTruckActivity(
                            $this->truck_id,
                            'maintenance',
                            null,
                            $truck->currentArea->area_name ?? 'Unknown',
                            "Compartment {$compartmentData['compartment_no']} capacity changed from {$oldCapacity}L to {$compartmentData['capacity_ltrs']}L"
                        );
                    }

                    if ($oldLoaded != $compartmentData['loaded_ltrs']) {
                        $fuelDiff = $compartmentData['loaded_ltrs'] - $oldLoaded;
                        $action = $fuelDiff > 0 ? 'fuel_added' : 'fuel_removed';
                        $this->logTruckActivity(
                            $this->truck_id,
                            $action,
                            abs($fuelDiff),
                            $truck->currentArea->area_name ?? 'Unknown',
                            "Compartment {$compartmentData['compartment_no']}: " . ($fuelDiff > 0 ? "Added" : "Removed") . " " . abs($fuelDiff) . "L"
                        );
                    }
                }
            } else {
                $newCompartment = TruckCompartment::create([
                    'truck_id' => $this->truck_id,
                    'compartment_no' => $compartmentData['compartment_no'],
                    'current_fuel_type_id' => $compartmentData['current_fuel_type_id'],
                    'capacity_ltrs' => floatval($compartmentData['capacity_ltrs']),
                    'loaded_ltrs' => floatval($compartmentData['loaded_ltrs']),
                    'available_ltrs' => floatval($compartmentData['capacity_ltrs']) - floatval($compartmentData['loaded_ltrs']),
                ]);

                $this->logTruckActivity(
                    $this->truck_id,
                    'maintenance',
                    null,
                    $truck->currentArea->area_name ?? 'Unknown',
                    "Added new compartment: {$compartmentData['compartment_no']} with capacity {$compartmentData['capacity_ltrs']}L"
                );
            }
        }

        // Delete compartments that were removed
        $truck->compartments()->whereNotIn('id', $existingCompartmentIds)->delete();

        session()->flash('message', 'Truck updated successfully!');
        return redirect()->route('admin.trucks.index');
    }

    public function render()
    {
        return view('livewire.truck-management.truck-edit', [
            'remainingCapacity' => $this->remainingCapacity,
            'totalCompartmentCapacity' => $this->totalCompartmentCapacity,
        ]);
    }
}