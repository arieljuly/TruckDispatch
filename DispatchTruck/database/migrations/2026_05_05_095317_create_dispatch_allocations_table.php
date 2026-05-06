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
        Schema::create('dispatch_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dispatch_session_id')->constrained('dispatch_sessions')->onDelete('cascade');
            $table->foreignId('truck_id')->constrained('trucks')->onDelete('cascade');
            $table->foreignId('area_id')->constrained('areas')->onDelete('cascade');
            $table->decimal('liters_allocated', 10, 2);
            $table->decimal('distance_used', 10, 2);
            $table->boolean('is_primary_area')->default(false);
            $table->enum('status', ['pending', 'completed', 'failed'])->default('pending');
            $table->timestamps();

            // Indexes
            $table->index('dispatch_session_id');
            $table->index('truck_id');
            $table->index('area_id');
            $table->index('status');
            $table->index(['dispatch_session_id', 'truck_id']);
            $table->index(['truck_id', 'area_id']);
            $table->index(['is_primary_area', 'dispatch_session_id']);

            // Composite unique to prevent duplicate allocations in same session
            $table->unique(['dispatch_session_id', 'truck_id', 'area_id'], 'unique_session_truck_area');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dispatch_allocations');
    }
};
