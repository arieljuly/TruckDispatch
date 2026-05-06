<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     */
    public const HOME = '/login';

    /**
     * Get the path that users should be redirected to after login based on their role.
     */
    public static function home(): string
    {
        if (auth()->check()) {
            $user = auth()->user();

            if ($user->isAdmin()) {
                return '/admin/dashboard';
            }

            if ($user->isDispatcher()) {
                return '/dispatcher/dashboard';
            }

            if ($user->isDriver()) {
                return '/driver/dashboard';
            }

            if ($user->isClient()) {
                return '/client/dashboard';
            }
        }

        return self::HOME;
    }

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        $this->routes(function () {
            // Load API routes only if the file exists
            if (file_exists(base_path('routes/api.php'))) {
                Route::middleware('api')
                    ->prefix('api')
                    ->group(base_path('routes/api.php'));
            }

            // Load web routes (always exists)
            if (file_exists(base_path('routes/web.php'))) {
                Route::middleware('web')
                    ->group(base_path('routes/web.php'));
            }
        });
    }
}