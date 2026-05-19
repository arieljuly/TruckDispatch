<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FuelPredictionService
{
    protected $apiUrl;
    protected $timeout;

    public function __construct()
    {
        $this->apiUrl = config('services.fastapi.url', 'http://localhost:8000');
        $this->timeout = config('services.fastapi.timeout', 5);
    }

    /**
     * Predict fuel consumption using Python ML API
     */
    public function predict(array $data): array
    {
        try {
            // Prepare request data
            $requestData = [
                'distance_km' => (float) $data['distance_km'],
                'actual_duration_hours' => (float) $data['actual_duration_hours'],
                'average_mpg' => isset($data['average_mpg']) ? (float) $data['average_mpg'] : 6.0,
                'idle_time_hours' => (float) ($data['idle_time_hours'] ?? 0),
                'detention_minutes' => (int) ($data['detention_minutes'] ?? 0),
                'delay_minutes' => (int) ($data['delay_minutes'] ?? 0),
                'on_time_flag' => (bool) ($data['on_time_flag'] ?? true),
            ];

            Log::info('Calling Python prediction API', [
                'url' => $this->apiUrl . '/api/v1/predict/fuel',
                'data' => $requestData
            ]);

            // Call Python API
            $response = Http::timeout($this->timeout)
                ->post($this->apiUrl . '/api/v1/predict/fuel', $requestData);

            if ($response->successful()) {
                $result = $response->json();

                Log::info('Python prediction successful', [
                    'predicted_fuel' => $result['predicted_fuel_liters'],
                    'confidence' => $result['confidence_score']
                ]);

                return [
                    'predicted_fuel_liters' => $result['predicted_fuel_liters'],
                    'confidence_score' => $result['confidence_score'],
                    'model_version' => $result['model_version'],
                    'prediction_interval_lower' => $result['prediction_interval_lower'],
                    'prediction_interval_upper' => $result['prediction_interval_upper'],
                    'feature_importance' => $result['feature_importance'] ?? [],
                    'is_fallback' => false
                ];
            } else {
                Log::warning('Python API returned error', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return $this->fallbackPrediction($data);
            }

        } catch (\Exception $e) {
            Log::error('Python API call failed', [
                'error' => $e->getMessage(),
                'api_url' => $this->apiUrl
            ]);
            return $this->fallbackPrediction($data);
        }
    }

    /**
     * Fallback prediction using PHP calculations (when Python API is unavailable)
     */
    protected function fallbackPrediction(array $data): array
    {
        Log::info('Using fallback prediction', $data);

        // Calculate driving fuel
        $averageMpg = $data['average_mpg'] ?? 6.0;
        if ($averageMpg <= 0)
            $averageMpg = 6.0;

        $drivingFuel = $data['distance_km'] / $averageMpg;

        // Additional fuel factors
        $idleFuel = ($data['idle_time_hours'] ?? 0) * 2.0;
        $detentionFuel = (($data['detention_minutes'] ?? 0) / 60) * 1.5;
        $delayFuel = (($data['delay_minutes'] ?? 0) / 60) * 2.5;

        // On-time adjustment
        $onTimeFlag = $data['on_time_flag'] ?? true;
        $onTimeAdjustment = $onTimeFlag ? 0.95 : 1.10;

        // Calculate total
        $totalFuel = ($drivingFuel + $idleFuel + $detentionFuel + $delayFuel) * $onTimeAdjustment;

        // Add 10% safety margin
        $predictedFuel = $totalFuel * 1.1;

        // Calculate prediction interval (85% confidence for fallback)
        $stdDev = 0.15 * $predictedFuel;
        $lowerBound = $predictedFuel - 1.44 * $stdDev;
        $upperBound = $predictedFuel + 1.44 * $stdDev;

        return [
            'predicted_fuel_liters' => round($predictedFuel, 2),
            'confidence_score' => 0.75,
            'model_version' => 'fallback-v1.0',
            'prediction_interval_lower' => round(max(0, $lowerBound), 2),
            'prediction_interval_upper' => round($upperBound, 2),
            'is_fallback' => true,
            'feature_importance' => [
                'driving_fuel' => round($drivingFuel / max($totalFuel, 1) * 100, 1),
                'idle_fuel' => round($idleFuel / max($totalFuel, 1) * 100, 1),
                'detention_fuel' => round($detentionFuel / max($totalFuel, 1) * 100, 1),
                'delay_fuel' => round($delayFuel / max($totalFuel, 1) * 100, 1)
            ]
        ];
    }

    /**
     * Check if Python API is healthy
     */
    public function isHealthy(): bool
    {
        try {
            $response = Http::timeout(2)->get($this->apiUrl . '/health');
            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }
}