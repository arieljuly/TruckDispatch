<?php

namespace App\Services;

use App\Models\DispatchSession;
use App\Models\DispatchAllocation;
use App\Models\Truck;
use App\Models\Area;
use App\Models\DeliveryRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class DispatchManagementService
{
    protected $fuelPredictionService;
    protected $optimizationService;

    public function __construct(
        FuelPredictionService $fuelPredictionService,
        DispatchOptimizationService $optimizationService
    ) {
        $this->fuelPredictionService = $fuelPredictionService;
        $this->optimizationService = $optimizationService;
    }

    /**
     * Create a new dispatch session with AI recommendation
     */
    public function createDispatchSession(array $data): DispatchSession
    {
        return DB::transaction(function () use ($data) {
            // Check if prediction data is already provided (from Livewire component)
            $hasPredictionData = isset($data['predicted_fuel_liters']) && isset($data['prediction_confidence']);

            $prediction = null;
            $recommendedTruck = null;

            if (!$hasPredictionData) {
                // Get fuel prediction from service
                $prediction = $this->fuelPredictionService->predict([
                    'distance_km' => $data['distance_km'],
                    'actual_duration_hours' => $data['actual_duration_hours'],
                    'average_mpg' => $data['average_mpg'] ?? null,
                    'idle_time_hours' => $data['idle_time_hours'] ?? 0,
                    'detention_minutes' => $data['detention_minutes'] ?? 0,
                    'delay_minutes' => $data['delay_minutes'] ?? 0,
                    'on_time_flag' => $data['on_time_flag'] ?? true,
                ]);

                // Get truck recommendation
                $recommendedTruck = $this->optimizationService->recommendTruck(
                    $prediction['predicted_fuel_liters'],
                    $data['area_id'] ?? null,
                    $data['filters'] ?? []
                );
            }

            // Prepare notes with fallback indicator
            $notes = $data['notes'] ?? null;
            $isFallback = $data['is_fallback'] ?? ($prediction['is_fallback'] ?? false);

            if ($isFallback) {
                $notes = ($notes ? $notes . ' ' : '') . '[Fallback prediction used]';
            }

            // Get prediction values (either from provided data or from prediction service)
            $predictedFuelLiters = $data['predicted_fuel_liters'] ?? ($prediction['predicted_fuel_liters'] ?? null);
            $predictionConfidence = $data['prediction_confidence'] ?? ($prediction['confidence_score'] ?? null);
            $predictionModelVersion = $data['prediction_model_version'] ?? ($prediction['model_version'] ?? null);
            $predictionIntervalLower = $data['prediction_interval_lower'] ?? ($prediction['prediction_interval_lower'] ?? null);
            $predictionIntervalUpper = $data['prediction_interval_upper'] ?? ($prediction['prediction_interval_upper'] ?? null);

            // If intervals not provided but we have confidence, calculate them
            if (($predictionIntervalLower === null || $predictionIntervalUpper === null) && $predictedFuelLiters && $predictionConfidence) {
                $marginOfError = $predictedFuelLiters * (1 - $predictionConfidence);
                $predictionIntervalLower = $predictedFuelLiters - $marginOfError;
                $predictionIntervalUpper = $predictedFuelLiters + $marginOfError;
            }

            // Create dispatch session with all fields
            $sessionData = [
                'algorithm_used' => $data['algorithm_used'] ?? 'greedy',
                'total_demand' => $predictedFuelLiters,
                'total_supply' => $recommendedTruck ? $recommendedTruck->available_ltrs : ($data['total_supply'] ?? 0),
                'status' => 'pending',
                'notes' => $notes,
                'recommended_truck_id' => $data['recommended_truck_id'] ?? ($recommendedTruck?->id),
                'predicted_fuel_liters' => $predictedFuelLiters,
                'prediction_confidence' => $predictionConfidence,
                'prediction_interval_lower' => $predictionIntervalLower,
                'prediction_interval_upper' => $predictionIntervalUpper,
                'optimization_method' => $data['optimization_method'] ?? ($recommendedTruck ? 'ai_ml' : null),
                'prediction_model_version' => $predictionModelVersion,
                'distance_km' => $data['distance_km'],
                'actual_duration_hours' => $data['actual_duration_hours'],
                'average_mpg' => $data['average_mpg'] ?? null,
                'idle_time_hours' => $data['idle_time_hours'] ?? 0,
                'detention_minutes' => $data['detention_minutes'] ?? 0,
                'delay_minutes' => $data['delay_minutes'] ?? 0,
                'on_time_flag' => $data['on_time_flag'] ?? true,
            ];

            // Add area_id if provided
            if (isset($data['area_id'])) {
                $sessionData['area_id'] = $data['area_id'];
            }

            $session = DispatchSession::create($sessionData);

            Log::info('Dispatch session created', [
                'session_id' => $session->id,
                'predicted_fuel' => $predictedFuelLiters,
                'confidence' => $predictionConfidence,
                'recommended_truck' => $recommendedTruck?->id,
                'is_fallback' => $isFallback
            ]);

            return $session;
        });
    }

    /**
     * Execute dispatch by assigning a truck
     */
    public function executeDispatch(DispatchSession $session, int $truckId, ?int $areaId = null): DispatchSession
    {
        return DB::transaction(function () use ($session, $truckId, $areaId) {
            $truck = Truck::findOrFail($truckId);

            // Verify truck can fulfill the dispatch
            if (!$this->optimizationService->canTruckFulfill($truck, $session->predicted_fuel_liters)) {
                throw new \Exception('Selected truck cannot fulfill this dispatch');
            }

            // Update session
            $session->assigned_truck_id = $truckId;
            $session->executed_by = Auth::id();
            $session->status = 'executed';
            $session->save();

            // Create allocation - using 'liters_allocated' column
            $allocation = DispatchAllocation::create([
                'dispatch_session_id' => $session->id,
                'truck_id' => $truckId,
                'area_id' => $areaId,
                'liters_allocated' => $session->predicted_fuel_liters, // Changed from 'allocated_liters'
                'distance_used' => $session->distance_km,
                'is_primary_area' => true,
                'status' => 'pending',
            ]);

            // Update truck available liters
            $truck->available_ltrs -= $session->predicted_fuel_liters;
            $truck->save();

            Log::info('Dispatch executed', [
                'session_id' => $session->id,
                'truck_id' => $truckId,
                'allocated_liters' => $session->predicted_fuel_liters
            ]);

            return $session;
        });
    }

    /**
     * Create dispatch from delivery request
     */
    public function createFromDeliveryRequest(DeliveryRequest $request, array $additionalData = []): DispatchSession
    {
        $area = $request->area;

        // Get distance from depot or default location
        $distance = DB::table('distance_matrix')
            ->where('to_area_id', $area->id)
            ->value('distance') ?? 50; // Default 50km

        $duration = $distance / 40; // Assume 40km/h average speed

        $dispatchData = array_merge([
            'distance_km' => $distance,
            'actual_duration_hours' => $duration,
            'average_mpg' => 6.0, // Default fuel efficiency
            'area_id' => $area->id,
            'notes' => "From delivery request #{$request->id}",
            'algorithm_used' => 'greedy',
        ], $additionalData);

        return $this->createDispatchSession($dispatchData);
    }

    /**
     * Get dispatch session with recommendations and alternatives
     */
    public function getDispatchWithAlternatives(DispatchSession $session): array
    {
        $alternatives = $this->optimizationService->getRecommendations(
            $session->predicted_fuel_liters,
            $session->allocations->first()?->area_id,
            5
        );

        return [
            'session' => $session->load(['recommendedTruck', 'assignedTruck', 'executor']),
            'recommended_truck' => $session->recommendedTruck,
            'assigned_truck' => $session->assignedTruck,
            'alternative_trucks' => $alternatives,
            'prediction_details' => [
                'predicted_fuel_liters' => $session->predicted_fuel_liters,
                'confidence_score' => $session->prediction_confidence ?? null,
                'prediction_interval_lower' => $session->prediction_interval_lower ?? null,
                'prediction_interval_upper' => $session->prediction_interval_upper ?? null,
                'model_version' => $session->prediction_model_version,
                'distance_km' => $session->distance_km,
                'duration_hours' => $session->actual_duration_hours,
            ]
        ];
    }

    /**
     * Update dispatch session with actual fuel usage (for learning)
     */
    public function recordActualFuelUsage(DispatchSession $session, float $actualFuelUsed): void
    {
        // Add column if it doesn't exist (you may need to add this to your table)
        // For now, we'll store it in notes or create a new column
        $session->notes = ($session->notes ? $session->notes . ' ' : '') . "[Actual fuel used: {$actualFuelUsed}L]";
        $session->save();

        // Calculate prediction error
        $error = abs($actualFuelUsed - $session->predicted_fuel_liters);
        $errorPercentage = ($error / $session->predicted_fuel_liters) * 100;

        Log::info('Actual fuel usage recorded', [
            'session_id' => $session->id,
            'predicted' => $session->predicted_fuel_liters,
            'actual' => $actualFuelUsed,
            'error_percentage' => $errorPercentage
        ]);

        // Send feedback to ML model for retraining
        $this->sendFeedbackToModel($session, $actualFuelUsed);
    }

    /**
     * Send feedback to ML model for improvement
     */
    protected function sendFeedbackToModel(DispatchSession $session, float $actualFuelUsed): void
    {
        try {
            $feedbackData = [
                'prediction_id' => $session->id,
                'predicted_fuel_liters' => $session->predicted_fuel_liters,
                'actual_fuel_liters' => $actualFuelUsed,
                'distance_km' => $session->distance_km,
                'actual_duration_hours' => $session->actual_duration_hours,
                'average_mpg' => $session->average_mpg,
                'idle_time_hours' => $session->idle_time_hours,
                'detention_minutes' => $session->detention_minutes,
                'delay_minutes' => $session->delay_minutes,
                'on_time_flag' => $session->on_time_flag,
                'confidence_score' => $session->prediction_confidence,
            ];

        } catch (\Exception $e) {
            Log::warning('Could not send feedback to ML model', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Get analytics for dispatch performance
     */
    public function getAnalytics(array $dateRange = null): array
    {
        $query = DispatchSession::where('status', 'executed')
            ->whereNotNull('predicted_fuel_liters');

        if ($dateRange) {
            $query->whereBetween('created_at', [$dateRange['start'], $dateRange['end']]);
        }

        $sessions = $query->get();

        $totalPredicted = $sessions->sum('predicted_fuel_liters');

        // Calculate actual fuel from allocations
        $totalActual = 0;
        foreach ($sessions as $session) {
            $actualFromAllocations = $session->allocations()->sum('liters_allocated');
            $totalActual += $actualFromAllocations;
        }

        $averageError = $sessions->avg(function ($session) {
            $actualFromAllocations = $session->allocations()->sum('liters_allocated');
            if (!$actualFromAllocations || $actualFromAllocations <= 0)
                return null;
            return abs($actualFromAllocations - $session->predicted_fuel_liters) / $session->predicted_fuel_liters * 100;
        });

        return [
            'total_sessions' => $sessions->count(),
            'total_predicted_fuel' => round($totalPredicted, 2),
            'total_actual_fuel' => round($totalActual, 2),
            'total_savings' => round($totalPredicted - $totalActual, 2),
            'average_prediction_error_percentage' => round($averageError ?? 0, 2),
            'ai_optimization_rate' => round($sessions->whereNotNull('optimization_method')->count() / max($sessions->count(), 1) * 100, 2),
            'average_confidence_score' => round($sessions->avg('prediction_confidence') ?? 0, 4),
        ];
    }

    /**
     * Get prediction accuracy over time
     */
    public function getPredictionAccuracy(int $days = 30): array
    {
        $sessions = DispatchSession::where('status', 'executed')
            ->where('created_at', '>=', now()->subDays($days))
            ->whereNotNull('predicted_fuel_liters')
            ->with('allocations')
            ->get();

        $accuracyData = [];
        foreach ($sessions as $session) {
            $actualFuel = $session->allocations()->sum('liters_allocated');
            if ($actualFuel > 0) {
                $accuracyData[] = [
                    'date' => $session->created_at->format('Y-m-d'),
                    'predicted' => $session->predicted_fuel_liters,
                    'actual' => $actualFuel,
                    'confidence' => $session->prediction_confidence,
                    'error_percentage' => abs($actualFuel - $session->predicted_fuel_liters) / $session->predicted_fuel_liters * 100,
                ];
            }
        }

        return $accuracyData;
    }
}