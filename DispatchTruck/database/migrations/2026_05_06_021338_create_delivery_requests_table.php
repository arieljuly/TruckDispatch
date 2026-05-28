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
        Schema::create('delivery_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('area_id')->constrained()->onDelete('cascade');
            $table->foreignId('station_id')->constrained()->onDelete('cascade');
            $table->foreignId('purchase_order_item_id')->constrained()->onDelete('cascade');
            $table->decimal('requested_liters', 10, 2);
            $table->integer('priority')->default(1)->comment('1=Low, 2=Medium, 3=High, 4=Emergency');
            $table->enum('status', ['pending', 'partially_fulfilled', 'fulfilled', 'cancelled'])->default('pending');
            $table->unsignedBigInteger('requested_by');
            $table->datetime('requested_date')->useCurrent();
            $table->decimal('fulfilled_liters', 10, 2)->default(0);
            $table->datetime('deadline')->nullable();
            $table->timestamps();

            $table->foreign('requested_by')->references('id')->on('users')->onDelete('cascade');

            // Indexes for performance
            $table->index('area_id');
            $table->index('status');
            $table->index('priority');
            $table->index('deadline');
            $table->index(['status', 'priority']);
            $table->index(['area_id', 'status']);
            $table->index(['deadline', 'priority']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_requests');
    }
};
