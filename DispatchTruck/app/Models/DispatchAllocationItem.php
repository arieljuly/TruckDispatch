<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DispatchAllocationItem extends Model
{
    use SoftDeletes;
    
    protected $table = "dispatch_allocation_items";
    
    protected $fillable = [
        'dispatch_allocation_id',
        'purchase_order_item_id',
        'truck_compartment_id',
        'liters_allocated',
        'status',
    ];

    protected $casts = [
        'liters_allocated' => 'decimal:3',
    ];

    // Relationships
    public function dispatchAllocation()
    {
        return $this->belongsTo(DispatchAllocation::class, 'dispatch_allocation_id');
    }

    public function purchaseOrderItem()
    {
        return $this->belongsTo(PurchaseOrderItems::class, 'purchase_order_item_id');
    }

    public function truckCompartment()
    {
        return $this->belongsTo(TruckCompartments::class, 'truck_compartment_id');
    }

    // Helper to mark as delivered
    public function markAsDelivered()
    {
        $this->status = 'delivered';
        $this->save();
        
        // Update the purchase order item delivered amount
        $this->purchaseOrderItem->addDelivery($this->liters_allocated);
        
        // Update truck compartment loaded amount
        $compartment = $this->truckCompartment;
        $compartment->loaded_ltrs += $this->liters_allocated;
        $compartment->available_ltrs = $compartment->capacity_ltrs - $compartment->loaded_ltrs;
        $compartment->save();
    }
}