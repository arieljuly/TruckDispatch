<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrders extends Model
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
}
