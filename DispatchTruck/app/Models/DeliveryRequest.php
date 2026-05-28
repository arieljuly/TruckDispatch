<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryRequest extends Model
{
    protected $table = "delivery_requests";

    protected $fillable = [
        'area_id',
        'station_id',
        'purchase_order_item_id',
        'requested_liters',
        'priority',
        'status',
        'requested_by',
        'fulfilled_liters',
        'deadline',
        'notes',
        'additional_items',
    ];

    protected $casts = [
        'deadline' => 'datetime',
        'requested_liters' => 'decimal:3',
        'fulfilled_liters' => 'decimal:3',
        'additional_items' => 'array',
    ];

    // Relationships
    public function area()
    {
        return $this->belongsTo(Area::class, 'area_id');
    }

    public function station()
    {
        return $this->belongsTo(Station::class, 'station_id');
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function purchaseOrderItem()
    {
        return $this->belongsTo(PurchaseOrderItem::class, 'purchase_order_item_id');
    }

    // Get purchase order associated with this delivery request
    public function purchaseOrder()
    {
        if ($this->additional_items && isset($this->additional_items['purchase_order_id'])) {
            return $this->belongsTo(PurchaseOrder::class, 'additional_items->purchase_order_id');
        }
        return null;
    }

    // Get all fuel items from the purchase order
    public function getFuelItems()
    {
        $items = [];

        // Check if additional_items contains purchase_order_id
        if ($this->additional_items && isset($this->additional_items['purchase_order_id'])) {
            $purchaseOrderId = $this->additional_items['purchase_order_id'];

            // Get all items from the purchase order
            $poItems = PurchaseOrderItem::with('fuelType')
                ->where('purchase_order_id', $purchaseOrderId)
                ->get();

            foreach ($poItems as $poItem) {
                $items[] = (object) [
                    'id' => $poItem->id,
                    'fuel_type_id' => $poItem->fuel_type_id,
                    'fuel_type_name' => $poItem->fuelType->fuel_name ?? 'Unknown',
                    'fuel_type_code' => $poItem->fuelType->fuel_code ?? 'N/A',
                    'requested_liters' => (float) $poItem->qty_liters,
                    'fulfilled_liters' => (float) $poItem->delivered_ltrs,
                    'remaining_liters' => (float) $poItem->remaining_liters,
                    'status' => $poItem->status,
                ];
            }
        }
        // Fallback: if purchase_order_item_id is set directly
        elseif ($this->purchase_order_item_id) {
            $poItem = PurchaseOrderItem::with('fuelType')->find($this->purchase_order_item_id);
            if ($poItem) {
                $items[] = (object) [
                    'id' => $poItem->id,
                    'fuel_type_id' => $poItem->fuel_type_id,
                    'fuel_type_name' => $poItem->fuelType->fuel_name ?? 'Unknown',
                    'fuel_type_code' => $poItem->fuelType->fuel_code ?? 'N/A',
                    'requested_liters' => (float) $this->requested_liters,
                    'fulfilled_liters' => (float) $this->fulfilled_liters,
                    'remaining_liters' => (float) $this->remaining_liters,
                    'status' => $this->status,
                ];
            }
        }

        return $items;
    }

    // Get total requested liters from all items
    public function getTotalRequestedLitersAttribute()
    {
        $items = $this->getFuelItems();
        return array_sum(array_column($items, 'requested_liters'));
    }

    // Get total fulfilled liters from all items
    public function getTotalFulfilledLitersAttribute()
    {
        $items = $this->getFuelItems();
        return array_sum(array_column($items, 'fulfilled_liters'));
    }

    // Get total remaining liters from all items
    public function getTotalRemainingLitersAttribute()
    {
        $items = $this->getFuelItems();
        return array_sum(array_column($items, 'remaining_liters'));
    }

    // Helper methods
    public function getRemainingLitersAttribute()
    {
        return $this->requested_liters - $this->fulfilled_liters;
    }

    public function getIsFulfilledAttribute()
    {
        return $this->remaining_liters <= 0;
    }

    public function getProgressPercentageAttribute()
    {
        $totalRequested = $this->total_requested_liters;
        if ($totalRequested <= 0)
            return 0;
        return ($this->total_fulfilled_liters / $totalRequested) * 100;
    }

    public function updateStatus()
    {
        if ($this->total_remaining_liters <= 0) {
            $this->status = 'fulfilled';
        } elseif ($this->total_fulfilled_liters > 0) {
            $this->status = 'partially_fulfilled';
        } else {
            $this->status = 'pending';
        }
        $this->save();
    }
}