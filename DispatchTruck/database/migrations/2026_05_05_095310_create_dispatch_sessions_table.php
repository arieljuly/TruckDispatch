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
        Schema::create('dispatch_sessions', function (Blueprint $table) {
            $table->id();
            $table->timestamp('DateCreated')->useCurrent();
            $table->enum('algorithm_used', ['greedy', 'min_cost_flow', 'nearest_neighbor'])->default('greedy');
            $table->decimal('total_demand', 12, 2);
            $table->decimal('total_supply', 12, 2);
            $table->enum('status', ['pending', 'executed', 'failed'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('DateCreated');
            $table->index('status');
            $table->index(['algorithm_used', 'status']);
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
