<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CheckMLHealth extends Command
{
    protected $signature = 'ml:check-health';
    protected $description = 'Check if ML API is healthy';

    public function handle()
    {
        try {
            $response = Http::timeout(5)->get('http://localhost:8000/health');

            if ($response->successful()) {
                $data = $response->json();
                $this->info('✅ ML API is healthy');
                $this->info('Model loaded: ' . ($data['model_loaded'] ? 'Yes' : 'No'));
                $this->info('Mode: ' . $data['mode']);
                return Command::SUCCESS;
            } else {
                $this->error('❌ ML API returned error: ' . $response->status());
                Log::warning('ML API health check failed', ['status' => $response->status()]);
                return Command::FAILURE;
            }
        } catch (\Exception $e) {
            $this->error('❌ Cannot connect to ML API: ' . $e->getMessage());
            Log::error('ML API connection failed', ['error' => $e->getMessage()]);
            return Command::FAILURE;
        }
    }
}