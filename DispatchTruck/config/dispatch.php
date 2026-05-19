    <?php

    return [
        /*
        |--------------------------------------------------------------------------
        | Optimization Strategy
        |--------------------------------------------------------------------------
        | Options: smallest_fit, closest_fit, largest_fit
        */
        'optimization_strategy' => env('DISPATCH_OPTIMIZATION_STRATEGY', 'smallest_fit'),

        /*
        |--------------------------------------------------------------------------
        | Fuel Buffer Percentage
        |--------------------------------------------------------------------------
        | Additional fuel buffer for safety margin
        */
        'fuel_buffer_percentage' => env('DISPATCH_FUEL_BUFFER', 0.10),

        /*
        |--------------------------------------------------------------------------
        | Auto-execute Dispatch
        |--------------------------------------------------------------------------
        | Automatically execute dispatch when truck is assigned
        */
        'auto_execute' => env('DISPATCH_AUTO_EXECUTE', false),

        /*
        |--------------------------------------------------------------------------
        | Enable AI/ML Features
        |--------------------------------------------------------------------------
        */
        'enable_ai_ml' => env('DISPATCH_ENABLE_AI_ML', true),

        /*
        |--------------------------------------------------------------------------
        | Prediction Cache TTL (minutes)
        |--------------------------------------------------------------------------
        */
        'prediction_cache_ttl' => env('DISPATCH_PREDICTION_CACHE_TTL', 60),
    ];