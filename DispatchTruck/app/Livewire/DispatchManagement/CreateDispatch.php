<?php

namespace App\Livewire\DispatchManagement;

use App\Models\Area;
use App\Models\DispatchSession;
use App\Models\DispatchAllocation;
use App\Models\Truck;
use App\Services\DispatchManagementService;
use App\Services\FuelPredictionService;
use App\Services\DispatchOptimizationService;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Illuminate\Support\Facades\Log;
use App\Services\TruckEfficiencyLearningService;

class CreateDispatch extends Component
{
    public $step = 1;
    public $areaId;
    public $distance_km;
    public $duration_hours;
    public $average_mpg;
    public $idle_time_hours = 0;
    public $detention_minutes = 0;
    public $delay_minutes = 0;
    public $on_time_flag = true;
    public $notes;
    public $predicted_fuel_liters;
    public $confidence_score;
    public $model_version;
    public $is_fallback = false;
    public $recommended_truck_id;
    public $selected_truck_id;
    public $truck_recommendations = [];
    public $selected_truck_excess;
    public $selected_truck_distance;
    public $selected_truck_travel_cost;
    public $selected_truck_net_available;
    public $dispatch_session_id;

    // Efficiency learning properties
    public $efficiency_source = 'manual';
    public $efficiency_editable = true;
    public $efficiency_message = '';
    public $efficiency_confidence = 'low';
    public $efficiency_data_points = 0;

    protected $dispatchManagementService;
    protected $efficiencyLearningService;

    public function boot(
        DispatchManagementService $dispatchManagementService,
        TruckEfficiencyLearningService $efficiencyLearningService
    ) {
        $this->dispatchManagementService = $dispatchManagementService;
        $this->efficiencyLearningService = $efficiencyLearningService;
    }

    protected $rules = [
        'areaId' => 'required|exists:areas,id',
        'distance_km' => 'required|numeric|min:0.1',
        'duration_hours' => 'required|numeric|min:0.1',
        'average_mpg' => 'required|numeric|min:0.1|max:20',
        'idle_time_hours' => 'numeric|min:0',
        'detention_minutes' => 'integer|min:0',
        'delay_minutes' => 'integer|min:0',
        'on_time_flag' => 'boolean',
    ];

    public function render()
    {
        $areas = Area::where('status', 'active')->get();

        Log::info('CreateDispatch page loaded', [
            'user_id' => auth()->id(),
            'step' => $this->step,
            'areas_count' => $areas->count()
        ]);

        return view('livewire.dispatch-management.create-dispatch', [
            'areas' => $areas,
        ]);
    }
    

    // Auto-load efficiency when truck is selected
    public function updatedSelectedTruckId($truckId)
    {
        if (!$truckId)
            return;

        $truck = Truck::find($truckId);
        if (!$truck)
            return;

        // Get efficiency data from learning service
        $efficiencyData = $this->efficiencyLearningService->getEfficiencyForTruck($truck);

        if ($efficiencyData['value']) {
            $this->average_mpg = $efficiencyData['value'];
            $this->efficiency_source = $efficiencyData['source'];
            $this->efficiency_editable = $efficiencyData['editable'];
            $this->efficiency_message = $efficiencyData['message'];
            $this->efficiency_confidence = $efficiencyData['confidence'];
            $this->efficiency_data_points = $efficiencyData['data_points'] ?? 0;

            $this->dispatch('notify', [
                'message' => $efficiencyData['message'],
                'type' => $efficiencyData['source'] === 'manual_required' ? 'warning' : 'info'
            ]);
        }

        // Also update the travel cost calculation for the selected truck
        if ($this->selected_truck_id && $this->distance_km) {
            $this->recalculateTravelCost();
        }
    }

    // Recalculate travel cost when efficiency or distance changes
    public function updatedAverageMpg()
    {
        if ($this->selected_truck_id) {
            $this->recalculateTravelCost();
        }
    }

    public function updatedDistanceKm()
    {
        if ($this->selected_truck_id) {
            $this->recalculateTravelCost();
        }
    }

    protected function recalculateTravelCost()
    {
        $truck = Truck::find($this->selected_truck_id);
        if (!$truck)
            return;

        $optimizationService = app(DispatchOptimizationService::class);

        // Get distance to area
        $distanceToArea = $optimizationService->getDistanceBetweenAreas($truck->current_area_id, $this->areaId);

        // Calculate travel fuel based on current efficiency
        $fuelToReach = $distanceToArea / max($this->average_mpg, 0.1);
        $netAvailable = $truck->available_ltrs - $fuelToReach;

        $this->selected_truck_distance = $distanceToArea;
        $this->selected_truck_travel_cost = round($fuelToReach, 2);
        $this->selected_truck_net_available = round($netAvailable, 2);
        $this->selected_truck_excess = round($netAvailable - $this->predicted_fuel_liters, 2);
    }

    public function predictFuel()
    {
        // If no efficiency value, try to get from selected truck
        if (!$this->average_mpg && $this->selected_truck_id) {
            $truck = Truck::find($this->selected_truck_id);
            if ($truck && $truck->avg_fuel_efficiency) {
                $this->average_mpg = $truck->avg_fuel_efficiency;
                $this->efficiency_source = 'auto_filled';
            } else {
                $this->average_mpg = 6.0; // Default fallback
            }
        }

        $this->validate();

        try {
            $predictionService = app(FuelPredictionService::class);
            $prediction = $predictionService->predict([
                'distance_km' => $this->distance_km,
                'actual_duration_hours' => $this->duration_hours,
                'average_mpg' => $this->average_mpg,
                'idle_time_hours' => $this->idle_time_hours,
                'detention_minutes' => $this->detention_minutes,
                'delay_minutes' => $this->delay_minutes,
                'on_time_flag' => $this->on_time_flag,
            ]);

            $this->predicted_fuel_liters = $prediction['predicted_fuel_liters'];
            $this->confidence_score = $prediction['confidence_score'];
            $this->model_version = $prediction['model_version'];
            $this->is_fallback = $prediction['is_fallback'] ?? false;

            $this->step = 2;

            $this->dispatch('notify', [
                'message' => "AI predicts {$this->predicted_fuel_liters} liters of fuel needed.",
                'type' => 'success'
            ]);

        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'message' => 'Prediction failed: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    public function getRecommendations()
    {
        Log::info('getRecommendations called', [
            'predicted_fuel' => $this->predicted_fuel_liters,
            'area_id' => $this->areaId,
            'step_before' => $this->step
        ]);

        if (!$this->predicted_fuel_liters) {
            Log::error('No predicted fuel available before getting recommendations');
            $this->dispatch('notify', [
                'message' => 'Please predict fuel requirements first.',
                'type' => 'error'
            ]);
            return;
        }

        if (!$this->areaId) {
            Log::error('No area selected before getting recommendations');
            $this->dispatch('notify', [
                'message' => 'Please select a delivery area first.',
                'type' => 'error'
            ]);
            return;
        }

        try {
            $optimizationService = app(DispatchOptimizationService::class);

            // Pass the efficiency to optimization service
            $optimizationService->setFuelEfficiency($this->average_mpg ?? 6.0);

            $recommendations = $optimizationService->getRecommendations(
                $this->predicted_fuel_liters,
                $this->areaId,
                10
            );

            Log::info('Recommendations received', [
                'count' => $recommendations->count(),
                'has_recommendations' => $recommendations->isNotEmpty()
            ]);

            if ($recommendations->isEmpty()) {
                $allTrucks = Truck::where('status', 'active')->whereNull('deleted_at')->get();
                $maxAvailable = $allTrucks->max('available_ltrs');

                Log::warning('No trucks found with sufficient fuel', [
                    'required_fuel' => $this->predicted_fuel_liters,
                    'total_trucks' => $allTrucks->count(),
                    'max_available_fuel' => $maxAvailable
                ]);

                $this->dispatch('notify', [
                    'message' => "No trucks available with sufficient fuel. Required: {$this->predicted_fuel_liters}L, Max available: {$maxAvailable}L",
                    'type' => 'error'
                ]);

                $this->truck_recommendations = [];
                return;
            }

            $this->truck_recommendations = $recommendations;

            if ($this->truck_recommendations->isNotEmpty()) {
                $firstRec = $this->truck_recommendations->first();
                $this->recommended_truck_id = $firstRec->truck->id;
                $this->selected_truck_id = $this->recommended_truck_id;
                $this->selected_truck_excess = $firstRec->excess_capacity;
                $this->selected_truck_travel_cost = $firstRec->fuel_to_reach;
                $this->selected_truck_net_available = $firstRec->net_available;
                $this->selected_truck_distance = $firstRec->distance_km;

                // Auto-load efficiency for the recommended truck
                $this->updatedSelectedTruckId($this->recommended_truck_id);

                Log::info('Auto-selected best truck', [
                    'truck_id' => $this->selected_truck_id,
                    'truck_name' => $firstRec->truck->truck_name,
                    'net_available' => $this->selected_truck_net_available,
                    'excess' => $this->selected_truck_excess
                ]);
            }

            $this->step = 3;

            $this->dispatch('notify', [
                'message' => "Found " . $recommendations->count() . " suitable truck(s) for this dispatch.",
                'type' => 'success'
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting recommendations', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->dispatch('notify', [
                'message' => 'Error finding trucks: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    public function selectTruck($truckId)
    {
        $this->selected_truck_id = $truckId;

        $recommendation = collect($this->truck_recommendations)->firstWhere('truck.id', $truckId);
        if ($recommendation) {
            $this->selected_truck_excess = $recommendation->excess_capacity;
            $this->selected_truck_travel_cost = $recommendation->fuel_to_reach;
            $this->selected_truck_net_available = $recommendation->net_available;
            $this->selected_truck_distance = $recommendation->distance_km;
        }

        // Load efficiency data for selected truck
        $this->updatedSelectedTruckId($truckId);

        $this->dispatch('notify', [
            'message' => 'Truck selected successfully.',
            'type' => 'success'
        ]);
    }

    public function createDispatch()
    {
        $this->validate([
            'areaId' => 'required|exists:areas,id',
            'distance_km' => 'required|numeric|min:0.1',
            'duration_hours' => 'required|numeric|min:0.1',
            'average_mpg' => 'required|numeric|min:0.1|max:20',
        ]);

        if (!$this->selected_truck_id) {
            $this->dispatch('notify', [
                'message' => 'Please select a truck first.',
                'type' => 'error'
            ]);
            return;
        }

        $truck = Truck::find($this->selected_truck_id);

        // Check if truck has enough fuel INCLUDING travel cost
        if (!$truck || ($truck->available_ltrs - ($this->selected_truck_travel_cost ?? 0)) < $this->predicted_fuel_liters) {
            $this->dispatch('notify', [
                'message' => 'Selected truck does not have enough fuel (including travel to destination).',
                'type' => 'error'
            ]);
            return;
        }

        $dispatchSession = DB::transaction(function () use ($truck) {
            // Calculate prediction intervals
            $predictionIntervalLower = $this->predicted_fuel_liters * 0.85;
            $predictionIntervalUpper = $this->predicted_fuel_liters * 1.15;

            // Add travel cost and efficiency info to notes
            $travelNote = sprintf(
                "Travel to area: %s km (%.1f L fuel), Net available at destination: %.1f L | Efficiency: %.2f km/L (%s, %d trips) | Algorithm: greedy",
                number_format($this->selected_truck_distance ?? 0, 1),
                $this->selected_truck_travel_cost ?? 0,
                $this->selected_truck_net_available ?? 0,
                $this->average_mpg,
                $this->efficiency_source,
                $this->efficiency_data_points
            );

            $finalNotes = $this->notes
                ? $this->notes . "\n\n" . $travelNote
                : $travelNote;

            $session = DispatchSession::create([
                'distance_km' => $this->distance_km,
                'actual_duration_hours' => $this->duration_hours,
                'average_mpg' => $this->average_mpg,
                'idle_time_hours' => $this->idle_time_hours,
                'detention_minutes' => $this->detention_minutes,
                'delay_minutes' => $this->delay_minutes,
                'on_time_flag' => $this->on_time_flag,
                'notes' => $finalNotes,
                'algorithm_used' => 'greedy',
                'predicted_fuel_liters' => $this->predicted_fuel_liters,
                'prediction_confidence' => $this->confidence_score ?? 0.85,
                'prediction_interval_lower' => $predictionIntervalLower,
                'prediction_interval_upper' => $predictionIntervalUpper,
                'prediction_model_version' => $this->model_version ?? 'calc-v1.0',
                'recommended_truck_id' => $this->recommended_truck_id,
                'assigned_truck_id' => $truck->id,
                'executed_by' => auth()->id(),
                'status' => 'executed',
                'total_demand' => $this->predicted_fuel_liters,
                'total_supply' => $truck->available_ltrs,
            ]);

            DispatchAllocation::create([
                'dispatch_session_id' => $session->id,
                'truck_id' => $truck->id,
                'area_id' => $this->areaId,
                'liters_allocated' => $this->predicted_fuel_liters,
                'distance_used' => $this->distance_km,
                'is_primary_area' => true,
                'status' => 'pending',
            ]);

            // Deduct fuel including travel cost
            $totalFuelToDeduct = $this->predicted_fuel_liters + ($this->selected_truck_travel_cost ?? 0);
            $truck->available_ltrs -= $totalFuelToDeduct;

            // Record actual efficiency for learning
            $actualFuelUsed = $this->predicted_fuel_liters; // In real scenario, this would be actual measured
            $this->efficiencyLearningService->recordActualEfficiency(
                $truck,
                $this->distance_km,
                $actualFuelUsed
            );

            $truck->save();

            Log::info('Dispatch created with learning', [
                'session_id' => $session->id,
                'truck_id' => $truck->id,
                'required_fuel' => $this->predicted_fuel_liters,
                'travel_fuel' => $this->selected_truck_travel_cost,
                'total_deducted' => $totalFuelToDeduct,
                'efficiency_used' => $this->average_mpg,
                'efficiency_source' => $this->efficiency_source
            ]);

            return $session;
        });

        $this->dispatch_session_id = $dispatchSession->id;
        $this->step = 4;

        $this->dispatch('notify', [
            'message' => "Dispatch #{$dispatchSession->id} created successfully!",
            'type' => 'success'
        ]);
    }

    public function resetForm()
    {
        $this->reset();
        $this->step = 1;
        $this->truck_recommendations = [];
        $this->efficiency_source = 'manual';
        $this->efficiency_editable = true;
        $this->efficiency_message = '';

        $this->dispatch('notify', [
            'message' => 'Form reset. Ready to create a new dispatch.',
            'type' => 'info'
        ]);
    }

    public function previousStep()
    {
        if ($this->step > 1) {
            $this->step--;
        }
    }
}