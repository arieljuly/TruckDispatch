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
        Schema::create('trucks', function (Blueprint $table) {
            $table->id();
            $table->string('truck_name');
            $table->string('plate_number')->nullable()->unique();
            $table->foreignId('current_area_id')->nullable()->constrained('areas')->nullOnDelete();
            $table->enum('status', ['available', 'in_transit', 'maintenance'])->default('available');
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('current_area_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trucks');
    }
};
