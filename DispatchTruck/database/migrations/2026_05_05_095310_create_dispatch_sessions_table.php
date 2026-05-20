<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('dispatch_sessions', function (Blueprint $table) {
            $table->id();
            $table->timestamp('DateCreated')->useCurrent();

            // Algorithm & Optimization
            $table->enum('algorithm_used', ['greedy', 'min_cost_flow', 'nearest_neighbor'])->default('greedy');
            $table->string('optimization_method')->nullable();
            $table->string('prediction_model_version')->nullable();

            // Demand & Supply
            $table->decimal('total_demand', 12, 2);
            $table->decimal('total_supply', 12, 2);

            // Truck Assignment
            $table->unsignedBigInteger('recommended_truck_id')->nullable();
            $table->unsignedBigInteger('assigned_truck_id')->nullable();

            // Fuel Predictions
            $table->decimal('predicted_fuel_liters', 10, 2)->nullable();
            $table->decimal('actual_fuel_used', 10, 2)->nullable();
            $table->decimal('fuel_efficiency_km_per_liter', 8, 2)->nullable();
            $table->decimal('prediction_confidence', 5, 2)->nullable();
            $table->decimal('prediction_interval_lower', 10, 2)->nullable();
            $table->decimal('prediction_interval_upper', 10, 2)->nullable();

            // Performance Metrics
            $table->decimal('distance_km', 10, 2)->nullable();
            $table->decimal('actual_duration_hours', 8, 2)->nullable();
            $table->decimal('average_mpg', 8, 2)->nullable();
            $table->decimal('idle_time_hours', 8, 2)->nullable();
            $table->integer('detention_minutes')->nullable();
            $table->integer('delay_minutes')->nullable();
            $table->boolean('on_time_flag')->default(true);

            // Execution & Status
            $table->unsignedBigInteger('executed_by')->nullable();
            $table->enum('status', ['pending', 'executed', 'failed'])->default('pending');
            $table->text('notes')->nullable();

            $table->timestamps();

            // Foreign Keys
            $table->foreign('recommended_truck_id')->references('id')->on('trucks')->onDelete('set null');
            $table->foreign('assigned_truck_id')->references('id')->on('trucks')->onDelete('set null');
            $table->foreign('executed_by')->references('id')->on('users')->onDelete('set null');

            // Indexes
            $table->index('DateCreated');
            $table->index('status');
            $table->index(['algorithm_used', 'status']);
            $table->index('recommended_truck_id');
            $table->index('assigned_truck_id');
            $table->index('predicted_fuel_liters');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dispatch_sessions');
    }
};