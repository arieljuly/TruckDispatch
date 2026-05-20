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
        Schema::create('dispatch_allocation_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dispatch_allocation_id');
            $table->unsignedBigInteger('purchase_order_item_id');
            $table->unsignedBigInteger('truck_compartment_id');
            $table->decimal('liters_allocated', 12, 3);
            $table->enum('status', ['pending', 'in_transit', 'delivered', 'failed'])->default('pending');
            $table->timestamps();
            $table->foreign('dispatch_allocation_id')->references('id')->on('dispatch_allocations');
            $table->foreign('purchase_order_item_id')->references('id')->on('purchase_order_items');
            $table->foreign('truck_compartment_id')->references('id')->on('truck_compartments');

            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dispatch_allocation_items');
    }
};
