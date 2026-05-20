<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrderItem extends Model
{
    use SoftDeletes;
    protected $table = "purchase_order_items";
    protected $fillable = [
        'purchase_order_id',
        'fuel_type_id',
        'qty_liters',
        'delivered_ltrs',
        'unit_price',
        'status',
    ];

    protected $casts = [
        'qty_liters' => 'decimal:3',
        'delivered_ltrs' => 'decimal:3',
        'unit_price' => 'decimal:2',
    ];

    // Relationships
    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id');
    }

    public function fuelType()
    {
        return $this->belongsTo(FuelType::class, 'fuel_type_id');
    }

    public function dispatchAllocationItems()
    {
        return $this->hasMany(DispatchAllocationItem::class, 'purchase_order_item_id');
    }

    // Helper to get remaining liters to deliver
    public function getRemainingLitersAttribute()
    {
        return $this->qty_liters - $this->delivered_ltrs;
    }

    // Helper to check if item is fully delivered
    public function getIsFullyDeliveredAttribute()
    {
        return $this->remaining_liters <= 0;
    }

    // Helper to add delivery and update status
    public function addDelivery($liters)
    {
        $this->delivered_ltrs += $liters;

        if ($this->is_fully_delivered) {
            $this->status = 'completed';
        } elseif ($this->delivered_ltrs > 0) {
            $this->status = 'partial';
        }

        $this->save();

        // Update parent PO status
        $this->purchaseOrder->updateStatusFromItems();
    }
}