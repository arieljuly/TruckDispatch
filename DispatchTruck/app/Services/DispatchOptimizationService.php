<?php

namespace App\Services;

use App\Models\Truck;
use App\Models\Area;
use App\Models\Station;
use App\Models\DistanceMatrix;
use App\Models\TruckCompartment;
use App\Models\FuelType;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DispatchOptimizationService
{
    protected $apiUrl;
    protected $timeout;
    protected $fuelEfficiency;

    public function __construct()
    {
        $this->apiUrl = config('services.dispatch_api.url', 'http://localhost:8002');
        $this->timeout = config('services.dispatch_api.timeout', 30);
        $this->fuelEfficiency = config('services.dispatch_api.fuel_efficiency', 10.0);
    }

    /**
     * Get truck recommendations using greedy algorithm
     */
    public function getRecommendations(float $requiredFuel, ?int $areaId = null, int $limit = 5): Collection
    {
        Log::info('Getting truck recommendations (Greedy Algorithm)', [
            'required_fuel' => $requiredFuel,
            'target_area' => $areaId,
            'limit' => $limit
        ]);

        // Get all available trucks with their compartments
        $trucks = Truck::with(['compartments.fuelType', 'currentArea'])
            ->where('status', 'available')
            ->whereNull('deleted_at')
            ->get();

        if ($trucks->isEmpty()) {
            return collect();
        }

        $recommendations = [];

        foreach ($trucks as $truck) {
            // Calculate total available fuel across compartments
            $availableLtrs = $truck->compartments->sum('available_ltrs');

            // Calculate distance to target area
            $distanceToArea = $this->getDistanceBetweenAreas($truck->current_area_id, $areaId);

            // Calculate fuel needed to reach destination
            $fuelToReach = $this->calculateFuelToReachArea($distanceToArea, $truck);

            // Calculate net available fuel at destination
            $netAvailable = $availableLtrs - $fuelToReach;

            // Check if truck can fulfill the requirement
            if ($netAvailable >= $requiredFuel && $fuelToReach <= $availableLtrs) {
                // Get fuel types available in this truck
                $fuelTypes = $truck->compartments
                    ->filter(function ($comp) {
                        return $comp->available_ltrs > 0; })
                    ->map(function ($comp) {
                        return [
                            'fuel_type' => $comp->fuelType->fuel_code ?? 'unknown',
                            'available_ltrs' => $comp->available_ltrs
                        ];
                    })
                    ->values();

                $recommendations[] = (object) [
                    'truck' => $truck,
                    'required_fuel' => $requiredFuel,
                    'available_fuel' => $availableLtrs,
                    'fuel_to_reach' => round($fuelToReach, 2),
                    'net_available' => round($netAvailable, 2),
                    'distance_km' => round($distanceToArea, 2),
                    'fuel_types' => $fuelTypes,
                    'priority_score' => $this->calculatePriorityScore($truck, $requiredFuel, $fuelToReach, $distanceToArea),
                    'efficiency_km_per_l' => $this->getTruckEfficiency($truck)
                ];
            }
        }

        // Sort by priority score
        usort($recommendations, function ($a, $b) {
            return $b->priority_score <=> $a->priority_score;
        });

        return collect(array_slice($recommendations, 0, $limit));
    }

    /**
     * Get truck efficiency (km per liter)
     */
    protected function getTruckEfficiency(Truck $truck): float
    {
        // You can store efficiency in trucks table or calculate from logs
        return $truck->fuel_efficiency_km_per_l ?? $this->fuelEfficiency;
    }

    /**
     * Calculate fuel needed to reach target area
     */
    protected function calculateFuelToReachArea(float $distanceKm, Truck $truck): float
    {
        if ($distanceKm <= 0) {
            return 0;
        }

        $efficiency = $this->getTruckEfficiency($truck);
        return $distanceKm / $efficiency;
    }

    /**
     * Get distance between areas from distance matrix
     */
    public function getDistanceBetweenAreas(?int $fromAreaId, ?int $toAreaId): float
    {
        if (!$fromAreaId || !$toAreaId) {
            return 50;
        }

        if ($fromAreaId == $toAreaId) {
            return 0;
        }

        // Query distance matrix
        $distance = DistanceMatrix::where('from_area_id', $fromAreaId)
            ->where('to_area_id', $toAreaId)
            ->value('distance');

        if ($distance !== null) {
            return (float) $distance;
        }

        // Try reverse direction
        $reverseDistance = DistanceMatrix::where('from_area_id', $toAreaId)
            ->where('to_area_id', $fromAreaId)
            ->value('distance');

        if ($reverseDistance !== null) {
            return (float) $reverseDistance;
        }

        return 50; // Default fallback
    }

    /**
     * Calculate priority score for truck ranking
     */
    protected function calculatePriorityScore(Truck $truck, float $requiredFuel, float $fuelToReach, float $distance): float
    {
        $score = 0;
        $availableLtrs = $truck->compartments->sum('available_ltrs');
        $netAvailable = $availableLtrs - $fuelToReach;
        $excessFuel = $netAvailable - $requiredFuel;
        $excessRatio = $requiredFuel > 0 ? $excessFuel / $requiredFuel : 0;

        // Factor 1: Fuel efficiency (perfect match gets highest score)
        if ($excessRatio <= 0.1 && $excessRatio >= -0.05) {
            $score += 50;
        } elseif ($excessRatio <= 0.25) {
            $score += 40;
        } elseif ($excessRatio <= 0.5) {
            $score += 30;
        } elseif ($excessRatio <= 1.0) {
            $score += 20;
        } else {
            $score += 10;
        }

        // Factor 2: Distance to area
        if ($distance == 0) {
            $score += 30;
        } elseif ($distance <= 10) {
            $score += 25;
        } elseif ($distance <= 30) {
            $score += 20;
        } elseif ($distance <= 60) {
            $score += 10;
        } else {
            $score += 5;
        }

        // Factor 3: Truck efficiency bonus
        $efficiency = $this->getTruckEfficiency($truck);
        if ($efficiency >= 12) {
            $score += 15;
        } elseif ($efficiency >= 10) {
            $score += 10;
        } elseif ($efficiency >= 8) {
            $score += 5;
        }

        return min(100, $score);
    }

    /**
     * Validate if truck can serve a station (7 rules)
     */
    public function validateTruckForStation(Truck $truck, Station $station, int $currentLocationId, array $options = []): array
    {
        $defaults = [
            'driver_hours_used' => 0,
            'max_driver_hours' => 11
        ];
        $options = array_merge($defaults, $options);

        $validationResults = [];

        // Rule 1: Truck must be available
        $rule1 = $truck->status === 'available';
        $validationResults['truck_available'] = $rule1;
        if (!$rule1) {
            return $this->validationResult(false, 'SKIP', "Truck is {$truck->status}", $validationResults);
        }

        // Rule 2: Truck must have remaining capacity
        $totalAvailable = $truck->compartments->sum('available_ltrs');
        $rule2 = $totalAvailable > 0;
        $validationResults['has_capacity'] = $rule2;
        if (!$rule2) {
            return $this->validationResult(false, 'SKIP', 'No available capacity', $validationResults);
        }

        // Rule 3: Fuel type compatibility
        $rule3 = true;
        $fuelCheckDetails = [];

        // Get required fuel from station's pending purchase orders
        $requiredFuels = $this->getStationRequiredFuels($station);

        foreach ($requiredFuels as $fuelType => $requiredQty) {
            $availableForFuel = $truck->compartments
                ->where('current_fuel_type_id', $this->getFuelTypeId($fuelType))
                ->sum('available_ltrs');

            if ($availableForFuel == 0) {
                $rule3 = false;
                $fuelCheckDetails[] = "No {$fuelType} available";
            } elseif ($availableForFuel < $requiredQty) {
                $rule3 = false;
                $fuelCheckDetails[] = "Insufficient {$fuelType}: need {$requiredQty}L, have {$availableForFuel}L";
            }
        }

        $validationResults['fuel_compatible'] = $rule3;
        if (!$rule3) {
            return $this->validationResult(false, 'REASSIGN', implode(', ', $fuelCheckDetails), $validationResults);
        }

        // Rule 4: Distance feasibility
        $distance = $this->getDistanceBetweenAreas($currentLocationId, $station->area_id);
        $efficiency = $this->getTruckEfficiency($truck);
        $fuelToReach = $distance / $efficiency;

        $rule4 = $fuelToReach <= $totalAvailable;
        $validationResults['can_reach'] = $rule4;
        if (!$rule4) {
            return $this->validationResult(false, 'SKIP', "Cannot reach: needs {$fuelToReach}L, has {$totalAvailable}L", $validationResults);
        }

        // Rule 5: Cost effectiveness (placeholder - will be checked in route optimization)
        $rule5 = true;
        $validationResults['cost_effective'] = $rule5;

        // Rule 6: Driver hours
        $estimatedHours = $distance / 50;
        $rule6 = $options['driver_hours_used'] + $estimatedHours <= $options['max_driver_hours'];
        $validationResults['driver_hours_ok'] = $rule6;
        if (!$rule6) {
            return $this->validationResult(false, 'REASSIGN', "Driver hours exceeded", $validationResults);
        }

        // Rule 7: No higher priority (handled by caller)
        $validationResults['no_higher_priority'] = true;

        $netAvailable = $totalAvailable - $fuelToReach;

        return $this->validationResult(true, 'ASSIGN', 'All validation passed', $validationResults, [
            'distance_km' => $distance,
            'fuel_to_reach' => round($fuelToReach, 2),
            'net_available' => round($netAvailable, 2)
        ]);
    }

    /**
     * Validate truck's ability to continue to next station
     */
    public function validateContinueToNextStation(
        Truck $truck,
        Station $currentStation,
        Station $nextStation,
        float $remainingCapacity,
        Collection $alternativeTrucks
    ): array {
        $distance = $this->getDistanceBetweenAreas($currentStation->area_id, $nextStation->area_id);

        // Check remaining capacity
        if ($remainingCapacity <= 0) {
            return ['should_continue' => false, 'reason' => 'No remaining capacity'];
        }

        // Check fuel type for next station
        $nextRequiredFuels = $this->getStationRequiredFuels($nextStation);
        foreach ($nextRequiredFuels as $fuelType => $requiredQty) {
            $available = $truck->compartments
                ->where('current_fuel_type_id', $this->getFuelTypeId($fuelType))
                ->sum('available_ltrs');

            if ($available < $requiredQty) {
                return ['should_continue' => false, 'reason' => "Insufficient {$fuelType} for next station"];
            }
        }

        // Check if distance is acceptable
        $maxDistance = 150;
        if ($distance > $maxDistance) {
            return ['should_continue' => false, 'reason' => "Next station too far ({$distance}km)"];
        }

        // Cost effectiveness check
        $efficiency = $this->getTruckEfficiency($truck);
        $currentCost = $distance / $efficiency;

        $minAlternativeCost = PHP_FLOAT_MAX;
        foreach ($alternativeTrucks as $altTruck) {
            if ($altTruck->id != $truck->id) {
                $altEfficiency = $this->getTruckEfficiency($altTruck);
                $altCost = $distance / $altEfficiency;
                if ($altCost < $minAlternativeCost) {
                    $minAlternativeCost = $altCost;
                }
            }
        }

        $savings = $minAlternativeCost - $currentCost;
        $threshold = 0.2; // 20%

        if ($minAlternativeCost != PHP_FLOAT_MAX && $currentCost > $minAlternativeCost * (1 + $threshold)) {
            return [
                'should_continue' => false,
                'reason' => "Cheaper to use another truck (cost: {$currentCost}L vs {$minAlternativeCost}L)",
                'savings' => round($savings, 2),
                'current_cost' => round($currentCost, 2),
                'alternative_cost' => round($minAlternativeCost, 2)
            ];
        }

        return [
            'should_continue' => true,
            'reason' => "Continue OK - {$currentCost}L fuel needed",
            'savings' => round($savings, 2),
            'current_cost' => round($currentCost, 2),
            'alternative_cost' => round($minAlternativeCost, 2)
        ];
    }

    /**
     * Get required fuels for a station from pending purchase orders
     */
    protected function getStationRequiredFuels(Station $station): array
    {
        $required = [];

        foreach ($station->purchaseOrders as $po) {
            foreach ($po->items as $item) {
                $remaining = $item->qty_liters - $item->delivered_ltrs;
                if ($remaining > 0 && $item->fuelType) {
                    $fuelCode = $item->fuelType->fuel_code;
                    $required[$fuelCode] = ($required[$fuelCode] ?? 0) + $remaining;
                }
            }
        }

        return $required;
    }

    /**
     * Get fuel type ID by code
     */
    protected function getFuelTypeId(string $fuelCode): ?int
    {
        return FuelType::where('fuel_code', $fuelCode)->value('id');
    }

    /**
     * Format validation result
     */
    protected function validationResult(bool $canAssign, string $decision, string $reason, array $checks, array $extra = []): array
    {
        return array_merge([
            'can_assign' => $canAssign,
            'decision' => $decision,
            'reason' => $reason,
            'validation_checks' => $checks
        ], $extra);
    }

    /**
     * Call Python API for full optimization
     */
    public function runGreedyOptimization(
        Collection $trucks,
        Collection $areas,
        Collection $distances,
        ?int $startAreaId = null,
        string $optimizationMode = 'greedy'
    ): ?array {
        try {
            // Format trucks for API
            $formattedTrucks = $trucks->map(function ($truck) {
                return [
                    'id' => $truck->id,
                    'name' => $truck->truck_name,
                    'plate_number' => $truck->plate_number,
                    'current_area_id' => $truck->current_area_id,
                    'max_capacity_ltrs' => $truck->max_capacity_ltrs,
                    'fuel_efficiency_km_per_l' => $this->getTruckEfficiency($truck),
                    'status' => $truck->status,
                    'available_ltrs' => $truck->compartments->sum('available_ltrs'),
                    'compartments' => $truck->compartments->map(function ($comp) {
                        return [
                            'compartment_no' => $comp->compartment_no,
                            'fuel_type' => $comp->fuelType->fuel_code ?? 'diesel',
                            'capacity_ltrs' => $comp->capacity_ltrs,
                            'loaded_ltrs' => $comp->loaded_ltrs,
                            'available_ltrs' => $comp->available_ltrs
                        ];
                    })->toArray()
                ];
            })->toArray();

            // Format areas with stations
            $formattedAreas = $areas->map(function ($area) {
                return [
                    'id' => $area->id,
                    'name' => $area->area_name,
                    'stations' => $area->stations->map(function ($station) {
                        return [
                            'id' => $station->id,
                            'name' => $station->station_name,
                            'area_id' => $station->area_id,
                            'required_fuels' => $this->getStationRequiredFuels($station)
                        ];
                    })->toArray()
                ];
            })->toArray();

            // Format distances
            $formattedDistances = $distances->map(function ($dist) {
                return [
                    'from_area_id' => $dist->from_area_id,
                    'to_area_id' => $dist->to_area_id,
                    'distance_km' => $dist->distance
                ];
            })->toArray();

            $payload = [
                'trucks' => $formattedTrucks,
                'areas' => $formattedAreas,
                'distances' => $formattedDistances,
                'start_area_id' => $startAreaId,
                'optimization_mode' => $optimizationMode
            ];

            Log::info('Calling greedy optimization API', [
                'url' => $this->apiUrl . '/api/v2/dispatch/optimize',
                'trucks_count' => count($formattedTrucks),
                'areas_count' => count($formattedAreas),
                'mode' => $optimizationMode
            ]);

            $response = Http::timeout($this->timeout)
                ->post($this->apiUrl . '/api/v2/dispatch/optimize', $payload);

            if ($response->successful()) {
                $result = $response->json();
                Log::info('Optimization successful', [
                    'session_id' => $result['session_id'],
                    'fulfillment_rate' => $result['summary']['fulfillment_rate_percent']
                ]);
                return $result;
            } else {
                Log::error('Optimization API error', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return null;
            }

        } catch (\Exception $e) {
            Log::error('Optimization API exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * Validate truck for station via API
     */
    public function apiValidateTruckForStation(
        Truck $truck,
        Station $station,
        int $currentLocationId,
        float $driverHoursUsed = 0
    ): ?array {
        try {
            $payload = [
                'truck' => [
                    'id' => $truck->id,
                    'name' => $truck->truck_name,
                    'plate_number' => $truck->plate_number,
                    'current_area_id' => $truck->current_area_id,
                    'max_capacity_ltrs' => $truck->max_capacity_ltrs,
                    'fuel_efficiency_km_per_l' => $this->getTruckEfficiency($truck),
                    'status' => $truck->status,
                    'available_ltrs' => $truck->compartments->sum('available_ltrs'),
                    'compartments' => $truck->compartments->map(function ($comp) {
                        return [
                            'compartment_no' => $comp->compartment_no,
                            'fuel_type' => $comp->fuelType->fuel_code ?? 'diesel',
                            'capacity_ltrs' => $comp->capacity_ltrs,
                            'loaded_ltrs' => $comp->loaded_ltrs,
                            'available_ltrs' => $comp->available_ltrs
                        ];
                    })->toArray()
                ],
                'station' => [
                    'id' => $station->id,
                    'name' => $station->station_name,
                    'area_id' => $station->area_id,
                    'required_fuels' => $this->getStationRequiredFuels($station)
                ],
                'current_location_id' => $currentLocationId,
                'driver_hours_used' => $driverHoursUsed,
                'max_driver_hours' => 11
            ];

            $response = Http::timeout($this->timeout)
                ->post($this->apiUrl . '/api/v2/dispatch/validate', $payload);

            if ($response->successful()) {
                return $response->json();
            }

            return null;

        } catch (\Exception $e) {
            Log::error('Validation API error', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Check if truck can fulfill dispatch (accounting for travel)
     */
    public function canTruckFulfill(Truck $truck, float $requiredFuel, ?int $areaId = null): bool
    {
        $distanceToArea = $areaId ? $this->getDistanceBetweenAreas($truck->current_area_id, $areaId) : 0;
        $fuelToReach = $this->calculateFuelToReachArea($distanceToArea, $truck);
        $totalAvailable = $truck->compartments->sum('available_ltrs');
        $netAvailable = $totalAvailable - $fuelToReach;

        return $netAvailable >= $requiredFuel;
    }

    /**
     * Format recommendations for display
     */
    public function formatRecommendationsForDisplay(Collection $recommendations): array
    {
        return $recommendations->map(function ($rec) {
            return [
                'truck_id' => $rec->truck->id,
                'truck_name' => $rec->truck->truck_name,
                'plate_number' => $rec->truck->plate_number,
                'available_fuel' => $rec->available_fuel,
                'net_available_at_destination' => $rec->net_available,
                'fuel_to_reach' => $rec->fuel_to_reach,
                'distance_km' => $rec->distance_km,
                'priority_score' => $rec->priority_score,
                'efficiency' => $rec->efficiency_km_per_l,
                'fuel_types' => $rec->fuel_types,
                'can_fulfill' => $rec->net_available >= $rec->required_fuel,
                'excess_percentage' => round(($rec->net_available - $rec->required_fuel) / $rec->required_fuel * 100, 1)
            ];
        })->toArray();
    }
}