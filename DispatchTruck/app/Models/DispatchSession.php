<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;

class DispatchSession extends Model
{
    protected $table = "dispatch_sessions";

    protected $fillable = [
        'algorithm_used',
        'total_demand',
        'total_supply',
        'status',
        'notes',
        'recommended_truck_id',
        'assigned_truck_id',
        'predicted_fuel_liters',
        'prediction_confidence',
        'prediction_interval_lower',
        'prediction_interval_upper',
        'optimization_method',
        'prediction_model_version',
        'executed_by',
        'distance_km',
        'actual_duration_hours',
        'average_mpg',
        'idle_time_hours',
        'detention_minutes',
        'delay_minutes',
        'on_time_flag',
        'actual_fuel_used',
        'fuel_efficiency_km_per_l',
        'completion_notes',
    ];

    protected $casts = [
        'total_demand' => 'decimal:2',
        'total_supply' => 'decimal:2',
        'predicted_fuel_liters' => 'decimal:2',
        'prediction_confidence' => 'decimal:4',
        'prediction_interval_lower' => 'decimal:2',
        'prediction_interval_upper' => 'decimal:2',
        'distance_km' => 'decimal:2',
        'actual_duration_hours' => 'decimal:2',
        'average_mpg' => 'decimal:2',
        'idle_time_hours' => 'decimal:2',
        'detention_minutes' => 'integer',
        'delay_minutes' => 'integer',
        'on_time_flag' => 'boolean',
        'actual_fuel_used' => 'decimal:2',
        'fuel_efficiency_km_per_l' => 'decimal:2',
        'DateCreated' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $attributes = [
        'status' => 'pending',
        'on_time_flag' => true,
    ];

    // ==================== RELATIONSHIPS ====================

    public function recommendedTruck(): BelongsTo
    {
        return $this->belongsTo(Truck::class, 'recommended_truck_id');
    }

    public function assignedTruck(): BelongsTo
    {
        return $this->belongsTo(Truck::class, 'assigned_truck_id');
    }

    public function executor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'executed_by');
    }

    public function allocations(): HasMany
    {
        return $this->hasMany(DispatchAllocation::class, 'dispatch_session_id');
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class, 'area_id');
    }

    // ==================== SCOPES ====================

    /**
     * Scope for executed dispatches only
     */
    public function scopeExecuted(Builder $query): Builder
    {
        return $query->where('status', 'executed');
    }

    /**
     * Scope for pending dispatches
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for failed dispatches
     */
    public function scopeFailed(Builder $query): Builder
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope for AI-optimized dispatches
     */
    public function scopeAiOptimized(Builder $query): Builder
    {
        return $query->whereNotNull('optimization_method')
            ->whereIn('optimization_method', ['ai_ml', 'machine_learning', 'fuel_prediction']);
    }

    /**
     * Scope for dispatches with high prediction variance
     */
    public function scopeWithHighVariance(Builder $query, float $threshold = 20): Builder
    {
        return $query->whereNotNull('predicted_fuel_liters')
            ->whereHas('allocations', function ($q) use ($threshold) {
                $q->selectRaw('ABS(SUM(liters_allocated) - dispatch_sessions.predicted_fuel_liters) / dispatch_sessions.predicted_fuel_liters * 100 > ?', [$threshold]);
            });
    }

    /**
     * Scope for dispatches within date range
     */
    public function scopeDateBetween(Builder $query, string $startDate, string $endDate): Builder
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Scope for dispatches by truck
     */
    public function scopeByTruck(Builder $query, int $truckId): Builder
    {
        return $query->where('assigned_truck_id', $truckId)
            ->orWhere('recommended_truck_id', $truckId);
    }

    /**
     * Scope for high confidence predictions
     */
    public function scopeHighConfidence(Builder $query, float $minConfidence = 0.8): Builder
    {
        return $query->where('prediction_confidence', '>=', $minConfidence);
    }

    // ==================== ACCESSORS & MUTATORS ====================

    /**
     * Get the prediction accuracy percentage
     */
    public function getPredictionAccuracyAttribute(): ?float
    {
        if (!$this->actual_fuel_used || $this->actual_fuel_used <= 0) {
            return null;
        }

        $error = abs($this->actual_fuel_used - $this->predicted_fuel_liters);
        $errorPercentage = ($error / $this->predicted_fuel_liters) * 100;

        return round(100 - $errorPercentage, 2);
    }

    /**
     * Get the fuel savings (predicted vs actual)
     */
    public function getFuelSavingsAttribute(): ?float
    {
        if (!$this->actual_fuel_used) {
            return null;
        }

        return round($this->predicted_fuel_liters - $this->actual_fuel_used, 2);
    }

    /**
     * Get the fuel savings percentage
     */
    public function getFuelSavingsPercentageAttribute(): ?float
    {
        if (!$this->actual_fuel_used || $this->predicted_fuel_liters <= 0) {
            return null;
        }

        $savings = $this->predicted_fuel_liters - $this->actual_fuel_used;
        return round(($savings / $this->predicted_fuel_liters) * 100, 2);
    }

    /**
     * Get optimization efficiency (AI vs manual)
     */
    public function getOptimizationEfficiencyAttribute(): ?float
    {
        if (!$this->predicted_fuel_liters || !$this->assignedTruck) {
            return null;
        }

        $actualFuelUsed = $this->allocations()->sum('liters_allocated');

        if ($actualFuelUsed > 0) {
            $savings = $this->predicted_fuel_liters - $actualFuelUsed;
            return round(($savings / $this->predicted_fuel_liters) * 100, 2);
        }

        return null;
    }

    /**
     * Get the formatted status with badge class
     */
    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'executed' => '<span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">✓ Executed</span>',
            'pending' => '<span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">⏳ Pending</span>',
            'failed' => '<span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">✗ Failed</span>',
            default => '<span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">' . ucfirst($this->status) . '</span>',
        };
    }

    /**
     * Get the confidence level as text
     */
    public function getConfidenceLevelAttribute(): string
    {
        $confidence = $this->prediction_confidence ?? 0;

        if ($confidence >= 0.9)
            return 'Very High';
        if ($confidence >= 0.8)
            return 'High';
        if ($confidence >= 0.7)
            return 'Medium';
        if ($confidence >= 0.6)
            return 'Low';
        return 'Very Low';
    }

    /**
     * Get formatted date
     */
    public function getFormattedDateAttribute(): string
    {
        return $this->created_at ? $this->created_at->format('M d, Y H:i') : 'N/A';
    }

    /**
     * Get short summary of dispatch
     */
    public function getShortSummaryAttribute(): string
    {
        return sprintf(
            'Dispatch #%d: %.1f km → %.1f L fuel | Status: %s',
            $this->id,
            $this->distance_km ?? 0,
            $this->predicted_fuel_liters ?? 0,
            $this->status
        );
    }

    // ==================== BUSINESS LOGIC METHODS ====================

    /**
     * Check if dispatch used AI optimization
     */
    public function usedAiOptimization(): bool
    {
        return !is_null($this->optimization_method) &&
            in_array($this->optimization_method, ['ai_ml', 'machine_learning', 'fuel_prediction']);
    }

    /**
     * Mark dispatch as completed with actual fuel usage
     */
    public function completeWithActualFuel(float $actualFuelUsed, ?string $completionNotes = null): bool
    {
        try {
            $this->actual_fuel_used = $actualFuelUsed;
            $this->status = 'executed';

            // Calculate actual efficiency
            if ($this->distance_km > 0 && $actualFuelUsed > 0) {
                $this->actual_efficiency = $this->distance_km / $actualFuelUsed;
            }

            if ($completionNotes) {
                $this->completion_notes = $completionNotes;
            }

            $this->save();

            Log::info('Dispatch completed', [
                'dispatch_id' => $this->id,
                'actual_fuel' => $actualFuelUsed,
                'predicted_fuel' => $this->predicted_fuel_liters,
                'accuracy' => $this->prediction_accuracy
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Failed to complete dispatch', [
                'dispatch_id' => $this->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Calculate prediction error
     */
    public function calculatePredictionError(): ?float
    {
        if (!$this->actual_fuel_used || $this->predicted_fuel_liters <= 0) {
            return null;
        }

        $error = abs($this->actual_fuel_used - $this->predicted_fuel_liters);
        return round(($error / $this->predicted_fuel_liters) * 100, 2);
    }

    /**
     * Check if dispatch was efficient (saved fuel)
     */
    public function wasEfficient(): ?bool
    {
        if (!$this->actual_fuel_used) {
            return null;
        }

        return $this->actual_fuel_used < $this->predicted_fuel_liters;
    }

    /**
     * Get efficiency rating
     */
    public function getEfficiencyRatingAttribute(): string
    {
        if (!$this->wasEfficient()) {
            return 'Inefficient';
        }

        $savingsPercent = $this->fuel_savings_percentage;

        if ($savingsPercent >= 20)
            return 'Excellent';
        if ($savingsPercent >= 10)
            return 'Good';
        if ($savingsPercent >= 5)
            return 'Average';
        return 'Below Average';
    }

    // ==================== STATIC METHODS ====================

    /**
     * Get statistics for dashboard
     */
    public static function getStats(): array
    {
        return [
            'total' => self::count(),
            'executed' => self::executed()->count(),
            'pending' => self::pending()->count(),
            'failed' => self::failed()->count(),
            'total_fuel_predicted' => self::sum('predicted_fuel_liters'),
            'total_fuel_actual' => self::sum('actual_fuel_used'),
            'ai_optimized_count' => self::aiOptimized()->count(),
        ];
    }

    /**
     * Get average prediction accuracy over time
     */
    public static function getAverageAccuracy(int $days = 30): ?float
    {
        $sessions = self::where('status', 'executed')
            ->whereNotNull('actual_fuel_used')
            ->where('created_at', '>=', now()->subDays($days))
            ->get();

        if ($sessions->isEmpty()) {
            return null;
        }

        $totalAccuracy = 0;
        foreach ($sessions as $session) {
            $accuracy = $session->prediction_accuracy;
            if ($accuracy !== null) {
                $totalAccuracy += $accuracy;
            }
        }

        return round($totalAccuracy / $sessions->count(), 2);
    }
}