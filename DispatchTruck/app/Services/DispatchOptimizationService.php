<?php

namespace App\Services;

use App\Models\Truck;
use App\Models\Area;
use App\Models\DistanceMatrix;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DispatchOptimizationService
{
    protected $fuelEfficiency = 6.0; // km per liter

    /**
     * Set fuel efficiency for calculations
     */
    public function setFuelEfficiency(float $efficiency): void
    {
        $this->fuelEfficiency = $efficiency;
        Log::debug('Fuel efficiency set for optimization', ['efficiency' => $efficiency]);
    }

    /**
     * Get truck recommendations based on AVAILABLE fuel AND distance to area
     */
    public function getRecommendations(float $requiredFuel, ?int $areaId = null, int $limit = 5): Collection
    {
        Log::info('===== STARTING TRUCK RECOMMENDATION =====', [
            'required_fuel' => $requiredFuel,
            'target_area' => $areaId,
            'fuel_efficiency' => $this->fuelEfficiency,
            'limit' => $limit
        ]);

        // Get target area
        $targetArea = $areaId ? Area::where('id', $areaId)->where('status', 'active')->first() : null;

        if (!$targetArea && $areaId) {
            Log::error('Target area not found or inactive', ['area_id' => $areaId]);
            return collect();
        }

        Log::info('Target area details', [
            'id' => $targetArea?->id,
            'name' => $targetArea?->area_name,
            'required_liters' => $targetArea?->required_liters
        ]);

        // Get all available trucks
        $trucks = Truck::where('status', 'available')
            ->whereNull('deleted_at')
            ->get();

        Log::info('Available trucks found', [
            'count' => $trucks->count(),
            'trucks' => $trucks->map(function ($truck) {
                return [
                    'id' => $truck->id,
                    'name' => $truck->truck_name,
                    'available_ltrs' => $truck->available_ltrs,
                    'current_area_id' => $truck->current_area_id
                ];
            })->toArray()
        ]);

        if ($trucks->isEmpty()) {
            Log::warning('No available trucks found in the system');
            return collect();
        }

        $recommendations = [];

        foreach ($trucks as $truck) {
            Log::info('---- Analyzing truck ----', [
                'truck_id' => $truck->id,
                'truck_name' => $truck->truck_name,
                'available_fuel' => $truck->available_ltrs,
                'current_area_id' => $truck->current_area_id
            ]);

            // Calculate distance to target area
            $distanceToArea = $this->getDistanceBetweenAreas($truck->current_area_id, $areaId);

            Log::info('Distance calculation result', [
                'truck_id' => $truck->id,
                'from_area' => $truck->current_area_id,
                'to_area' => $areaId,
                'distance_km' => $distanceToArea
            ]);

            // Calculate fuel needed to reach destination
            $fuelToReach = $this->calculateFuelToReachArea($distanceToArea);

            // Calculate net available fuel at destination
            $netAvailableFuel = $truck->available_ltrs - $fuelToReach;

            Log::info('Fuel calculations', [
                'truck_id' => $truck->id,
                'available_fuel' => $truck->available_ltrs,
                'travel_distance' => $distanceToArea,
                'fuel_for_travel' => $fuelToReach,
                'net_available_at_destination' => $netAvailableFuel,
                'required_fuel_at_destination' => $requiredFuel,
                'difference' => $netAvailableFuel - $requiredFuel
            ]);

            // Check if truck has enough fuel to reach AND fulfill requirement
            $canReach = $fuelToReach <= $truck->available_ltrs;
            $canFulfill = $netAvailableFuel >= $requiredFuel;

            if ($canReach && $canFulfill) {
                $excessFuel = $netAvailableFuel - $requiredFuel;
                $excessPercentage = ($excessFuel / $requiredFuel) * 100;

                $recommendation = (object) [
                    'truck' => $truck,
                    'required_fuel' => $requiredFuel,
                    'available_fuel' => $truck->available_ltrs,
                    'fuel_to_reach' => round($fuelToReach, 2),
                    'net_available' => round($netAvailableFuel, 2),
                    'excess_capacity' => round($excessFuel, 2),
                    'excess_percentage' => round($excessPercentage, 1),
                    'distance_km' => round($distanceToArea, 2),
                    'priority_score' => $this->calculatePriorityScore($truck, $requiredFuel, $fuelToReach, $distanceToArea),
                ];

                $recommendations[] = $recommendation;

                Log::info('✅ Truck QUALIFIED', [
                    'truck_id' => $truck->id,
                    'truck_name' => $truck->truck_name,
                    'net_available' => $netAvailableFuel,
                    'excess' => $excessFuel,
                    'excess_percentage' => $excessPercentage,
                    'priority_score' => $recommendation->priority_score
                ]);
            } else {
                $reason = !$canReach ? "Not enough fuel to reach destination (needs {$fuelToReach}L, has {$truck->available_ltrs}L)"
                    : "Not enough fuel after travel (net {$netAvailableFuel}L, needs {$requiredFuel}L)";

                Log::info('❌ Truck did NOT qualify', [
                    'truck_id' => $truck->id,
                    'truck_name' => $truck->truck_name,
                    'reason' => $reason,
                    'available' => $truck->available_ltrs,
                    'fuel_to_reach' => $fuelToReach,
                    'net_available' => $netAvailableFuel,
                    'required' => $requiredFuel,
                    'shortage' => $requiredFuel - $netAvailableFuel
                ]);
            }
        }

        // Sort by priority score (higher is better)
        usort($recommendations, function ($a, $b) {
            return $b->priority_score <=> $a->priority_score;
        });

        // Take only top N recommendations
        $topRecommendations = array_slice($recommendations, 0, $limit);

        Log::info('===== RECOMMENDATION SUMMARY =====', [
            'total_trucks_analyzed' => $trucks->count(),
            'qualified_trucks' => count($recommendations),
            'returning_trucks' => count($topRecommendations),
            'required_fuel' => $requiredFuel,
            'target_area' => $areaId,
            'top_recommendation' => !empty($topRecommendations) ? [
                'truck_id' => $topRecommendations[0]->truck->id,
                'truck_name' => $topRecommendations[0]->truck->truck_name,
                'net_available' => $topRecommendations[0]->net_available,
                'distance_km' => $topRecommendations[0]->distance_km,
                'fuel_to_reach' => $topRecommendations[0]->fuel_to_reach,
                'priority_score' => $topRecommendations[0]->priority_score
            ] : 'NONE'
        ]);

        return collect($topRecommendations);
    }

    /**
     * Calculate fuel needed to reach target area based on distance
     */
    protected function calculateFuelToReachArea(float $distanceKm): float
    {
        if ($distanceKm <= 0) {
            return 0;
        }

        $fuelNeeded = $distanceKm / $this->fuelEfficiency;

        Log::debug('Travel fuel calculation', [
            'distance_km' => $distanceKm,
            'efficiency_km_per_liter' => $this->fuelEfficiency,
            'fuel_needed_liters' => round($fuelNeeded, 2)
        ]);

        return $fuelNeeded;
    }

    /**
     * Get distance between two areas from distance_matrix table
     */
    public function getDistanceBetweenAreas(?int $fromAreaId, ?int $toAreaId): float
    {
        // If either area ID is missing, return default
        if (!$fromAreaId || !$toAreaId) {
            Log::debug('Missing area IDs for distance calculation', [
                'from_area_id' => $fromAreaId,
                'to_area_id' => $toAreaId
            ]);
            return 50; // Default 50km
        }

        // If same area, distance is 0
        if ($fromAreaId == $toAreaId) {
            Log::debug('Same area, distance is 0', [
                'area_id' => $fromAreaId
            ]);
            return 0;
        }

        // Query the distance_matrix table for the distance
        $distanceRecord = DistanceMatrix::where('from_area_id', $fromAreaId)
            ->where('to_area_id', $toAreaId)
            ->first();

        if ($distanceRecord && $distanceRecord->distance !== null) {
            Log::debug('Distance found in matrix', [
                'from_area' => $fromAreaId,
                'to_area' => $toAreaId,
                'distance_km' => $distanceRecord->distance
            ]);
            return (float) $distanceRecord->distance;
        }

        // Try reverse direction (distance is usually symmetric)
        $reverseRecord = DistanceMatrix::where('from_area_id', $toAreaId)
            ->where('to_area_id', $fromAreaId)
            ->first();

        if ($reverseRecord && $reverseRecord->distance !== null) {
            Log::debug('Distance found in reverse matrix (using same value)', [
                'from_area' => $fromAreaId,
                'to_area' => $toAreaId,
                'distance_km' => $reverseRecord->distance
            ]);
            return (float) $reverseRecord->distance;
        }

        // Default fallback distance if no matrix entry found
        Log::warning('Distance not found in distance_matrix table, using default', [
            'from_area' => $fromAreaId,
            'to_area' => $toAreaId,
            'default_distance_km' => 50
        ]);

        return 50; // Default 50km fallback
    }

    /**
     * Calculate priority score (0-100) for truck ranking
     */
    protected function calculatePriorityScore(Truck $truck, float $requiredFuel, float $fuelToReach, float $distance): float
    {
        $score = 0;

        // Factor 1: Fuel efficiency (less waste is better)
        $netAvailable = $truck->available_ltrs - $fuelToReach;
        $excessFuel = $netAvailable - $requiredFuel;
        $excessRatio = $excessFuel / $requiredFuel;

        // Perfect match (0-10% excess) gets highest score
        if ($excessRatio <= 0.1) {
            $score += 50;
            Log::debug('Perfect match bonus', ['excess_ratio' => $excessRatio]);
        } elseif ($excessRatio <= 0.25) {
            $score += 40;
        } elseif ($excessRatio <= 0.5) {
            $score += 30;
        } elseif ($excessRatio <= 1.0) {
            $score += 20;
        } else {
            $score += 10;
        }

        // Factor 2: Distance to area (shorter is better)
        if ($distance == 0) {
            $score += 30; // Same area
            Log::debug('Same area bonus', ['distance' => $distance]);
        } elseif ($distance <= 10) {
            $score += 25;
        } elseif ($distance <= 30) {
            $score += 20;
        } elseif ($distance <= 60) {
            $score += 10;
        } else {
            $score += 5;
        }

        // Factor 3: Bonus for having exactly the right fuel (minimal waste)
        if ($excessFuel >= 0 && $excessFuel <= ($requiredFuel * 0.1)) {
            $score += 20;
            Log::debug('Minimal waste bonus', ['excess_fuel' => $excessFuel]);
        }

        $finalScore = min(100, $score);

        Log::debug('Priority score calculation', [
            'truck_id' => $truck->id,
            'truck_name' => $truck->truck_name,
            'excess_fuel' => round($excessFuel, 2),
            'excess_ratio' => round($excessRatio, 2),
            'distance_km' => $distance,
            'final_score' => $finalScore
        ]);

        return $finalScore;
    }

    /**
     * Check if truck can fulfill dispatch (accounting for travel)
     */
    public function canTruckFulfill(Truck $truck, float $requiredFuel, ?int $areaId = null): bool
    {
        $distanceToArea = $areaId ? $this->getDistanceBetweenAreas($truck->current_area_id, $areaId) : 0;
        $fuelToReach = $this->calculateFuelToReachArea($distanceToArea);
        $netAvailable = $truck->available_ltrs - $fuelToReach;

        $canFulfill = $netAvailable >= $requiredFuel;

        Log::info('Truck fulfill check', [
            'truck_id' => $truck->id,
            'required' => $requiredFuel,
            'net_available' => $netAvailable,
            'can_fulfill' => $canFulfill
        ]);

        return $canFulfill;
    }
}