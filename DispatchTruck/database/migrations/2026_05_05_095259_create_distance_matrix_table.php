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
        Schema::create('distance_matrix', function (Blueprint $table) {
            $table->id();
            $table->foreignId('from_area_id')->constrained('areas')->onDelete('cascade');
            $table->foreignId('to_area_id')->constrained('areas')->onDelete('cascade');
            $table->decimal('distance', 10, 2);
            $table->decimal('travel_time', 8, 2)->nullable()->comment('in minutes');
            $table->timestamps();

            $table->unique(['from_area_id', 'to_area_id']);

            // Indexes for quick lookups
            $table->index('from_area_id');
            $table->index('to_area_id');
            $table->index(['from_area_id', 'distance']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('distance_matrix');
    }
};
