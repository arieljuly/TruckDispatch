<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('maintenance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('truck_id')->constrained()->onDelete('cascade');
            $table->foreignId('reported_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('completed_by')->nullable()->constrained('users')->onDelete('set null');
            
            // Maintenance details
            $table->enum('maintenance_type', [
                'oil_change', 
                'repair', 
                'inspection', 
                'tire_replacement', 
                'brake_service',
                'engine_service',
                'transmission_service',
                'electrical_repair',
                'body_repair',
                'regular_maintenance'
            ])->default('regular_maintenance');
            
            $table->string('title')->nullable();
            $table->text('description');
            
            // Scheduling
            $table->date('scheduled_date');
            $table->date('start_date')->nullable();
            $table->date('completed_date')->nullable();
            
            // Measurements
            $table->integer('odometer_reading');
            
            // Cost tracking
            $table->decimal('estimated_cost', 10, 2)->default(0);
            $table->decimal('actual_cost', 10, 2)->nullable();
            
            // Status
            $table->enum('status', [
                'scheduled', 
                'in_progress', 
                'completed', 
                'cancelled', 
                'delayed'
            ])->default('scheduled');
            
            // Priority
            $table->enum('priority', ['low', 'medium', 'high', 'emergency'])->default('medium');
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for performance
            $table->index('truck_id');
            $table->index('maintenance_type');
            $table->index('status');
            $table->index('priority');
            $table->index('scheduled_date');
            $table->index('completed_date');
            $table->index(['truck_id', 'status']);
            $table->index(['status', 'scheduled_date']);
            $table->index(['truck_id', 'odometer_reading']);
            $table->index(['priority', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenances');
    }
};
