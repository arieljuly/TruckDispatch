<?php

namespace App\Providers;

use App\Services\DispatchManagementService;
use App\Services\DispatchOptimizationService;
use App\Services\FuelPredictionService;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register dispatch services as singletons
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

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(
            fn(): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}