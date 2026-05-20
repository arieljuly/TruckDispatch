<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrderItems extends Model
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
}
