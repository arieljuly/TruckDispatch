<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;

class Truck extends Model
{
    use SoftDeletes;

    protected $table = "trucks";

    protected $fillable = [
        'truck_name',
        'plate_number',
        'capacity_ltrs',
        'available_ltrs',
        'current_area_id',
        'status',
        // New efficiency tracking fields
        'avg_fuel_efficiency',
        'efficiency_data_points',
        'efficiency_std_dev',
        'efficiency_confidence',
        'total_distance_km',
        'total_fuel_used',
    ];

    protected $casts = [
        'capacity_ltrs' => 'decimal:2',
        'available_ltrs' => 'decimal:2',
        'avg_fuel_efficiency' => 'decimal:2',
        'efficiency_data_points' => 'integer',
        'efficiency_std_dev' => 'decimal:2',
        'total_distance_km' => 'decimal:2',
        'total_fuel_used' => 'decimal:2',
        'efficiency_confidence' => 'string',
    ];

    protected $attributes = [
        'efficiency_data_points' => 0,
        'total_distance_km' => 0,
        'total_fuel_used' => 0,
        'efficiency_confidence' => 'low',
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->whereNull('deleted_at');
    }

    public function scopeInactive($query)
    {
        return $query->whereNotNull('deleted_at');
    }

    public function scopeWithHighConfidence($query)
    {
        return $query->whereIn('efficiency_confidence', ['high', 'locked']);
    }

    public function scopeWithSufficientData($query, int $minTrips = 5)
    {
        return $query->where('efficiency_data_points', '>=', $minTrips);
    }

    // Relationships
    public function currentArea()
    {
        return $this->belongsTo(Area::class, 'current_area_id');
    }

    public function currentAssignment()
    {
        return $this->hasOne(TruckAssignment::class)->where('status', 'active')->latest('start_time');
    }

    public function assignments()
    {
        return $this->hasMany(TruckAssignment::class);
    }

    public function logs()
    {
        return $this->hasMany(TruckLog::class);
    }

    public function dispatchSessions()
    {
        return $this->hasMany(DispatchSession::class, 'assigned_truck_id');
    }

    public function recommendedDispatchSessions()
    {
        return $this->hasMany(DispatchSession::class, 'recommended_truck_id');
    }

    // Efficiency Methods
    public function updateEfficiency(float $distanceKm, float $fuelUsed): void
    {
        if ($fuelUsed <= 0 || $distanceKm <= 0) {
            Log::warning('Invalid data for efficiency update', [
                'truck_id' => $this->id,
                'distance' => $distanceKm,
                'fuel_used' => $fuelUsed
            ]);
            return;
        }

        $tripEfficiency = $distanceKm / $fuelUsed;

        // Update cumulative totals
        $this->total_distance_km += $distanceKm;
        $this->total_fuel_used += $fuelUsed;
        $this->efficiency_data_points++;

        // Calculate new average efficiency
        $this->avg_fuel_efficiency = $this->total_distance_km / $this->total_fuel_used;

        // Update standard deviation if we have enough data points
        if ($this->efficiency_data_points >= 2) {
            $this->updateStandardDeviation($tripEfficiency);
        }

        // Update confidence level based on data points
        $this->updateConfidenceLevel();

        $this->save();

        Log::info('Truck efficiency updated', [
            'truck_id' => $this->id,
            'trip_efficiency' => round($tripEfficiency, 2),
            'new_avg' => round($this->avg_fuel_efficiency, 2),
            'data_points' => $this->efficiency_data_points,
            'confidence' => $this->efficiency_confidence
        ]);
    }

    protected function updateStandardDeviation(float $newEfficiency): void
    {
        // Get recent efficiencies for standard deviation calculation
        $recentTrips = $this->dispatchSessions()
            ->where('status', 'executed')
            ->whereNotNull('actual_fuel_used')
            ->where('distance_km', '>', 0)
            ->orderBy('created_at', 'desc')
            ->take(20)
            ->get();

        if ($recentTrips->isEmpty()) {
            return;
        }

        $efficiencies = [];
        foreach ($recentTrips as $trip) {
            $efficiencies[] = $trip->distance_km / $trip->actual_fuel_used;
        }

        if (count($efficiencies) < 2) {
            return;
        }

        $mean = array_sum($efficiencies) / count($efficiencies);
        $variance = 0;

        foreach ($efficiencies as $efficiency) {
            $variance += pow($efficiency - $mean, 2);
        }

        $variance /= count($efficiencies);
        $this->efficiency_std_dev = sqrt($variance);
    }

    protected function updateConfidenceLevel(): void
    {
        $dataPoints = $this->efficiency_data_points;

        if ($dataPoints >= 50) {
            $this->efficiency_confidence = 'locked';
        } elseif ($dataPoints >= 20) {
            $this->efficiency_confidence = 'high';
        } elseif ($dataPoints >= 5) {
            $this->efficiency_confidence = 'medium';
        } else {
            $this->efficiency_confidence = 'low';
        }
    }

    // Get efficiency with confidence information
    public function getEfficiencyWithConfidence(): array
    {
        if ($this->efficiency_data_points === 0) {
            return [
                'value' => null,
                'source' => 'manual_required',
                'confidence' => 'low',
                'message' => 'No historical data. Please enter fuel efficiency manually.',
                'editable' => true,
                'data_points' => 0
            ];
        }

        if ($this->efficiency_data_points < 5) {
            return [
                'value' => round($this->avg_fuel_efficiency, 2),
                'source' => 'auto_suggest',
                'confidence' => 'low',
                'message' => "Based on {$this->efficiency_data_points} trip(s). You can modify if needed.",
                'editable' => true,
                'data_points' => $this->efficiency_data_points,
                'std_dev' => $this->efficiency_std_dev
            ];
        }

        if ($this->efficiency_data_points < 20) {
            $trend = $this->calculateTrend();
            return [
                'value' => round($this->avg_fuel_efficiency, 2),
                'source' => 'auto_filled',
                'confidence' => 'medium',
                'message' => "Auto-calculated from {$this->efficiency_data_points} trips. Trending: {$trend}",
                'editable' => true,
                'trend' => $trend,
                'data_points' => $this->efficiency_data_points,
                'std_dev' => $this->efficiency_std_dev
            ];
        }

        // High confidence or locked
        $confidenceScore = $this->calculateConfidenceScore();
        return [
            'value' => round($this->avg_fuel_efficiency, 2),
            'source' => $this->efficiency_confidence === 'locked' ? 'ai_locked' : 'auto_filled',
            'confidence' => $this->efficiency_confidence,
            'message' => "AI-calculated from {$this->efficiency_data_points} trips ({$confidenceScore}% confidence)",
            'editable' => $this->efficiency_confidence !== 'locked',
            'confidence_score' => $confidenceScore,
            'data_points' => $this->efficiency_data_points,
            'std_dev' => $this->efficiency_std_dev
        ];
    }

    protected function calculateTrend(): string
    {
        $trips = $this->dispatchSessions()
            ->where('status', 'executed')
            ->whereNotNull('actual_fuel_used')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        if ($trips->count() < 3) {
            return 'stable';
        }

        $recentAvg = 0;
        $olderAvg = 0;
        $half = floor($trips->count() / 2);

        foreach ($trips as $index => $trip) {
            $efficiency = $trip->distance_km / $trip->actual_fuel_used;
            if ($index < $half) {
                $recentAvg += $efficiency;
            } else {
                $olderAvg += $efficiency;
            }
        }

        $recentAvg /= $half;
        $olderAvg /= ($trips->count() - $half);

        $change = $recentAvg - $olderAvg;

        if ($change > 0.3)
            return 'improving 📈';
        if ($change < -0.3)
            return 'declining 📉';
        return 'stable 📊';
    }

    protected function calculateConfidenceScore(): float
    {
        $dataPoints = $this->efficiency_data_points;

        if ($dataPoints >= 50)
            return 99;
        if ($dataPoints >= 30)
            return 95;
        if ($dataPoints >= 20)
            return 90;
        if ($dataPoints >= 15)
            return 85;
        if ($dataPoints >= 10)
            return 80;
        if ($dataPoints >= 5)
            return 70;
        return 50;
    }

    // Check if truck has reliable efficiency data
    public function hasReliableEfficiency(): bool
    {
        return $this->efficiency_data_points >= 5 && $this->avg_fuel_efficiency !== null;
    }

    // Get efficiency for prediction (with fallback)
    public function getEfficiencyForPrediction(float $defaultEfficiency = 6.0): float
    {
        if ($this->hasReliableEfficiency()) {
            return $this->avg_fuel_efficiency;
        }

        return $defaultEfficiency;
    }

    // Calculate fuel needed for a trip based on truck's efficiency
    public function calculateFuelNeeded(float $distanceKm): float
    {
        $efficiency = $this->getEfficiencyForPrediction();

        if ($efficiency <= 0) {
            return $distanceKm / 6.0; // Fallback to 6 km/L
        }

        return $distanceKm / $efficiency;
    }

    // Get efficiency summary for display
    public function getEfficiencySummaryAttribute(): string
    {
        if (!$this->avg_fuel_efficiency) {
            return 'Not enough data';
        }

        $confidenceIcons = [
            'locked' => '🔒',
            'high' => '✅',
            'medium' => '📊',
            'low' => '⚠️'
        ];

        $icon = $confidenceIcons[$this->efficiency_confidence] ?? '📝';

        return sprintf(
            "%s %.2f km/L (%d trips)",
            $icon,
            $this->avg_fuel_efficiency,
            $this->efficiency_data_points
        );
    }

    // Accessor for backward compatibility
    public function getAverageMpgAttribute()
    {
        return $this->avg_fuel_efficiency;
    }
}