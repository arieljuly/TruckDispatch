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
        Schema::create('truck_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('truck_id')->constrained('trucks')->onDelete('cascade');
            $table->enum('action', ['created', 'assigned', 'loaded', 'delivered', 'maintenance', 'refueled', 'status_change', 'departed', 'arrived', 'returned', 'driver_assigned', 'driver_unassigned',]->default('created'));
            $table->decimal('liters', 10, 2)->nullable();
            $table->string('location')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamp('Timestamp')->useCurrent();
            $table->timestamps();

            // Indexes
            $table->index('truck_id');
            $table->index('action');
            $table->index('Timestamp');
            $table->index(['truck_id', 'Timestamp']);
            $table->index(['truck_id', 'action', 'Timestamp']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('truck_logs');
    }
};
