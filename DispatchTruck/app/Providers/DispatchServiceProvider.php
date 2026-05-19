<?php

namespace App\Providers;

use App\Services\DispatchManagementService;
use App\Services\DispatchOptimizationService;
use App\Services\FuelPredictionService;
use Illuminate\Support\ServiceProvider;

class DispatchServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(FuelPredictionService::class, function ($app) {
            return new FuelPredictionService();
        });

        $this->app->singleton(DispatchOptimizationService::class, function ($app) {
            return new DispatchOptimizationService(
                $app->make(FuelPredictionService::class)
            );
        });

        $this->app->singleton(DispatchManagementService::class, function ($app) {
            return new DispatchManagementService(
                $app->make(FuelPredictionService::class),
                $app->make(DispatchOptimizationService::class)
            );
        });
    }

    public function boot(): void
    {
        //
    }
}