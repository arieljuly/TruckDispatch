<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DispatchAllocationItems extends Model
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
}
