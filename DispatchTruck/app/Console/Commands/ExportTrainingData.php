<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\DispatchSession;
use App\Models\DispatchAllocation;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class ExportTrainingData extends Command
{
    protected $signature = 'ml:export-training 
                            {--send-to-api : Send data directly to Python API for retraining}
                            {--days=30 : Number of days of historical data to export}';

    protected $description = 'Export dispatch data for ML model training/retraining';

    public function handle()
    {
        $days = (int) $this->option('days');
        $sendToApi = $this->option('send-to-api');

        $this->info('📊 Fetching dispatch data from last ' . $days . ' days...');

        // Query dispatch sessions with actual fuel usage
        $sessions = DispatchSession::where('status', 'executed')
            ->whereNotNull('actual_fuel_used')
            ->where('distance_km', '>', 0)
            ->where('created_at', '>=', now()->subDays($days))
            ->with('allocations')
            ->get();

        if ($sessions->isEmpty()) {
            $this->warn('⚠️ No training data found in the last ' . $days . ' days');
            return Command::SUCCESS;
        }

        $this->info('✅ Found ' . $sessions->count() . ' dispatch records');

        $trainingData = [];
        foreach ($sessions as $session) {
            $actualFuelUsed = $session->actual_fuel_used ?? $session->allocations->sum('liters_allocated');

            $trainingData[] = [
                'distance_km' => (float) $session->distance_km,
                'actual_duration_hours' => (float) $session->actual_duration_hours,
                'average_mpg' => (float) ($session->average_mpg ?? 6.0),
                'idle_time_hours' => (float) ($session->idle_time_hours ?? 0),
                'detention_minutes' => (int) ($session->detention_minutes ?? 0),
                'delay_minutes' => (int) ($session->delay_minutes ?? 0),
                'on_time_flag' => $session->on_time_flag ? 1 : 0,
                'fuel_used_liters' => (float) $actualFuelUsed,
            ];
        }

        if ($sendToApi) {
            // Send to Python API for retraining
            $this->info('🔄 Sending ' . count($trainingData) . ' records to ML API for retraining...');

            try {
                $response = Http::timeout(300) // 5 minute timeout for training
                    ->post('http://localhost:8000/api/v1/model/retrain', [
                        'training_data' => $trainingData
                    ]);

                if ($response->successful()) {
                    $result = $response->json();
                    $this->info('✅ Model retrained successfully!');

                    $this->newLine();
                    $this->table(
                        ['Metric', 'Value'],
                        [
                            ['Status', $result['status'] ?? 'success'],
                            ['Model Version', $result['model_version'] ?? 'N/A'],
                            ['MAE', $result['metrics']['mae'] ?? 'N/A'],
                            ['R² Score', $result['metrics']['r2'] ?? 'N/A'],
                            ['Training Samples', $result['training_samples'] ?? count($trainingData)],
                        ]
                    );

                    Log::info('ML Model retrained', $result);
                } else {
                    $this->error('❌ Retraining failed: ' . $response->body());
                    Log::error('ML Model retraining failed', ['response' => $response->body()]);
                }
            } catch (\Exception $e) {
                $this->error('❌ Error calling ML API: ' . $e->getMessage());
                $this->warn('💡 Make sure your Python API is running: cd DispatchAlgo && python main.py');
                Log::error('ML API connection error', ['error' => $e->getMessage()]);
            }
        } else {
            // Save to CSV file
            $this->info('💾 Saving training data to CSV...');

            $csvPath = storage_path('ml/training_data_' . now()->format('Ymd_His') . '.csv');

            // Ensure directory exists
            if (!File::exists(storage_path('ml'))) {
                File::makeDirectory(storage_path('ml'), 0755, true);
            }

            $file = fopen($csvPath, 'w');

            // Write headers
            fputcsv($file, array_keys($trainingData[0]));

            // Write data rows
            foreach ($trainingData as $row) {
                fputcsv($file, $row);
            }
            fclose($file);

            $this->info('✅ Exported ' . count($trainingData) . ' records to:');
            $this->info('   ' . $csvPath);

            // Show sample of data
            $this->newLine();
            $this->info('📋 Sample of exported data:');
            $this->table(
                array_keys($trainingData[0]),
                array_slice($trainingData, 0, 5)
            );
        }

        return Command::SUCCESS;
    }
}