<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrder extends Model
{
    use SoftDeletes;
    protected $table = "purchase_orders";
    protected $fillable = [
        'user_id',
        'station_id',
        'po_number',
        'order_date',
        'request_delivery_date',
        'status',
    ];

    protected $casts = [
        'order_date' => 'date',
        'request_delivery_date' => 'date',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function station()
    {
        return $this->belongsTo(Station::class, 'station_id');
    }

    public function items()
    {
        return $this->hasMany(PurchaseOrderItems::class, 'purchase_order_id');
    }

    // Helper to get total ordered liters
    public function getTotalOrderedLitersAttribute()
    {
        return $this->items()->sum('qty_liters');
    }

    // Helper to get total delivered liters
    public function getTotalDeliveredLitersAttribute()
    {
        return $this->items()->sum('delivered_ltrs');
    }

    // Helper to check if PO is fully delivered
    public function getIsFullyDeliveredAttribute()
    {
        return $this->total_delivered_liters >= $this->total_ordered_liters;
    }

    // Helper to update PO status based on items
    public function updateStatusFromItems()
    {
        if ($this->is_fully_delivered) {
            $this->update(['status' => 'delivered']);
        } elseif ($this->total_delivered_liters > 0) {
            $this->update(['status' => 'partial']);
        }
    }
}