<?php

namespace App\Services;

use App\Models\Truck;
use App\Models\DispatchSession;
use App\Models\DispatchAllocation;
use Illuminate\Support\Facades\Log;

class TruckEfficiencyLearningService
{
    /**
     * Get efficiency value based on data confidence level
     */
    public function getEfficiencyForTruck(Truck $truck): array
    {
        $dataPoints = $truck->efficiency_data_points ?? 0;

        // PHASE 1: No data - Manual input required
        if ($dataPoints == 0) {
            return [
                'value' => null,
                'source' => 'manual_required',
                'confidence' => 'low',
                'message' => 'No historical data. Please enter fuel efficiency manually.',
                'editable' => true,
                'data_points' => 0
            ];
        }

        // PHASE 2: Low data (1-5 trips) - Auto-suggest with manual override
        if ($dataPoints < 5) {
            $calculated = $this->calculateAverageEfficiency($truck);
            return [
                'value' => round($calculated, 2),
                'source' => 'auto_suggest',
                'confidence' => 'low',
                'message' => "Based on {$dataPoints} trip(s). You can modify if needed.",
                'editable' => true,
                'data_points' => $dataPoints
            ];
        }

        // PHASE 3: Medium data (5-20 trips) - Auto-fill with warning
        if ($dataPoints < 20) {
            $calculated = $this->calculateWeightedEfficiency($truck);
            $trend = $this->calculateEfficiencyTrend($truck);
            return [
                'value' => round($calculated, 2),
                'source' => 'auto_filled',
                'confidence' => 'medium',
                'message' => "Auto-calculated from {$dataPoints} trips. Trending: {$trend}",
                'editable' => true,
                'trend' => $trend,
                'data_points' => $dataPoints
            ];
        }

        // PHASE 4: High data (20+ trips) - Locked, AI confident
        if ($dataPoints >= 20) {
            $calculated = $this->calculateAdvancedEfficiency($truck);
            $confidence = $this->calculateConfidenceLevel($truck);
            return [
                'value' => round($calculated, 2),
                'source' => 'ai_locked',
                'confidence' => 'high',
                'message' => "AI-calculated from {$dataPoints} trips ({$confidence}% confidence)",
                'editable' => false,
                'confidence_score' => $confidence,
                'data_points' => $dataPoints
            ];
        }

        return ['value' => 6.0, 'source' => 'fallback', 'editable' => true, 'data_points' => 0];
    }

    /**
     * Simple average for low data points
     * Uses allocations table for actual fuel used
     */
    protected function calculateAverageEfficiency(Truck $truck): float
    {
        // Get completed dispatches with allocations
        $dispatches = DispatchSession::where('assigned_truck_id', $truck->id)
            ->where('status', 'executed')
            ->where('distance_km', '>', 0)
            ->with('allocations')
            ->get();

        if ($dispatches->isEmpty()) {
            return 6.0; // Default fallback
        }

        $totalEfficiency = 0;
        $validTrips = 0;

        foreach ($dispatches as $dispatch) {
            // Get actual fuel used from allocations
            $actualFuelUsed = $dispatch->allocations->sum('liters_allocated');

            if ($actualFuelUsed > 0 && $dispatch->distance_km > 0) {
                $efficiency = $dispatch->distance_km / $actualFuelUsed;
                $totalEfficiency += $efficiency;
                $validTrips++;
            }
        }

        if ($validTrips === 0) {
            return 6.0;
        }

        return $totalEfficiency / $validTrips;
    }

    /**
     * Weighted average (recent trips matter more)
     */
    protected function calculateWeightedEfficiency(Truck $truck): float
    {
        $dispatches = DispatchSession::where('assigned_truck_id', $truck->id)
            ->where('status', 'executed')
            ->where('distance_km', '>', 0)
            ->with('allocations')
            ->orderBy('created_at', 'desc')
            ->get();

        if ($dispatches->isEmpty()) {
            return 6.0;
        }

        $weightedSum = 0;
        $totalWeight = 0;

        foreach ($dispatches as $index => $dispatch) {
            $actualFuelUsed = $dispatch->allocations->sum('liters_allocated');

            if ($actualFuelUsed > 0 && $dispatch->distance_km > 0) {
                $efficiency = $dispatch->distance_km / $actualFuelUsed;
                // Recent trips get higher weight (exponential decay)
                $tripWeight = pow(0.7, $index); // Weight decreases by 30% each trip
                $weightedSum += $efficiency * $tripWeight;
                $totalWeight += $tripWeight;
            }
        }

        if ($totalWeight === 0) {
            return 6.0;
        }

        return $weightedSum / $totalWeight;
    }

    /**
     * Advanced calculation using stored cumulative data
     */
    protected function calculateAdvancedEfficiency(Truck $truck): float
    {
        // Use stored cumulative data for efficiency
        if ($truck->total_fuel_used > 0 && $truck->total_distance_km > 0) {
            return $truck->total_distance_km / $truck->total_fuel_used;
        }

        return $this->calculateWeightedEfficiency($truck);
    }

    /**
     * Calculate efficiency trend (improving, stable, declining)
     */
    protected function calculateEfficiencyTrend(Truck $truck): string
    {
        $dispatches = DispatchSession::where('assigned_truck_id', $truck->id)
            ->where('status', 'executed')
            ->where('distance_km', '>', 0)
            ->with('allocations')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        if ($dispatches->count() < 3) {
            return 'stable 📊';
        }

        $recentEfficiencies = [];
        $olderEfficiencies = [];
        $half = floor($dispatches->count() / 2);

        foreach ($dispatches as $index => $dispatch) {
            $actualFuelUsed = $dispatch->allocations->sum('liters_allocated');

            if ($actualFuelUsed > 0 && $dispatch->distance_km > 0) {
                $efficiency = $dispatch->distance_km / $actualFuelUsed;

                if ($index < $half) {
                    $recentEfficiencies[] = $efficiency;
                } else {
                    $olderEfficiencies[] = $efficiency;
                }
            }
        }

        if (empty($recentEfficiencies) || empty($olderEfficiencies)) {
            return 'stable 📊';
        }

        $recentAvg = array_sum($recentEfficiencies) / count($recentEfficiencies);
        $olderAvg = array_sum($olderEfficiencies) / count($olderEfficiencies);
        $change = $recentAvg - $olderAvg;
        $changePercent = ($change / $olderAvg) * 100;

        if ($changePercent > 5)
            return 'improving 📈';
        if ($changePercent < -5)
            return 'declining 📉';
        return 'stable 📊';
    }

    /**
     * Calculate confidence level (0-100)
     */
    protected function calculateConfidenceLevel(Truck $truck): float
    {
        $dataPoints = $truck->efficiency_data_points;

        if ($dataPoints < 5)
            return 30;
        if ($dataPoints < 10)
            return 50;
        if ($dataPoints < 20)
            return 70;
        if ($dataPoints < 50)
            return 85;
        return 95;
    }

    /**
     * Record actual efficiency after dispatch completion
     */
    public function recordActualEfficiency(Truck $truck, float $distanceKm, float $fuelUsed): void
    {
        if ($fuelUsed <= 0 || $distanceKm <= 0) {
            Log::warning('Invalid data for efficiency recording', [
                'truck_id' => $truck->id,
                'distance' => $distanceKm,
                'fuel_used' => $fuelUsed
            ]);
            return;
        }

        $efficiency = $distanceKm / $fuelUsed;

        // Update cumulative totals
        $truck->total_distance_km = ($truck->total_distance_km ?? 0) + $distanceKm;
        $truck->total_fuel_used = ($truck->total_fuel_used ?? 0) + $fuelUsed;
        $truck->efficiency_data_points = ($truck->efficiency_data_points ?? 0) + 1;

        // Update average efficiency
        if ($truck->total_fuel_used > 0) {
            $truck->avg_fuel_efficiency = $truck->total_distance_km / $truck->total_fuel_used;
        }

        // Update confidence level
        $this->updateConfidenceLevel($truck);

        $truck->save();

        Log::info('Truck efficiency recorded', [
            'truck_id' => $truck->id,
            'trip_efficiency' => round($efficiency, 2),
            'new_avg' => round($truck->avg_fuel_efficiency ?? 0, 2),
            'data_points' => $truck->efficiency_data_points,
            'confidence' => $truck->efficiency_confidence
        ]);
    }

    protected function updateConfidenceLevel(Truck $truck): void
    {
        $dataPoints = $truck->efficiency_data_points ?? 0;

        if ($dataPoints >= 50) {
            $truck->efficiency_confidence = 'locked';
        } elseif ($dataPoints >= 20) {
            $truck->efficiency_confidence = 'high';
        } elseif ($dataPoints >= 5) {
            $truck->efficiency_confidence = 'medium';
        } else {
            $truck->efficiency_confidence = 'low';
        }
    }

    /**
     * Get efficiency summary for display
     */
    public function getEfficiencySummary(Truck $truck): string
    {
        $dataPoints = $truck->efficiency_data_points ?? 0;

        if ($dataPoints === 0) {
            return 'No data yet';
        }

        $avgEfficiency = $truck->avg_fuel_efficiency ?? 0;

        $icons = [
            'locked' => '🔒',
            'high' => '✅',
            'medium' => '📊',
            'low' => '⚠️'
        ];

        $icon = $icons[$truck->efficiency_confidence] ?? '📝';

        return sprintf("%s %.1f km/L (%d trips)", $icon, $avgEfficiency, $dataPoints);
    }
}