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
        Schema::create('truck_compartments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('truck_id')->constrained('trucks')->onDelete('cascade');
            $table->foreignId('current_fuel_type_id')->constrained('fuel_types')->onDelete('restrict');
            $table->string('compartment_no')->unique();
            $table->decimal('capacity_ltrs', 12, 3);
            $table->decimal('loaded_ltrs', 12, 3)->default(0);
            $table->decimal('available_ltrs', 12, 3)->default(0);
            $table->timestamps();
            $table->softDeletes();
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('truck_compartments');
    }
};
