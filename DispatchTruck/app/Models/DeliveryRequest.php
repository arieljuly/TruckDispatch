<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryRequest extends Model
{
    protected $table = "delivery_requests";
    protected $fillable = [
        'area_id',
        'requested_liters',
        'priority',
        'status', // 'pending', 'partially_fulfilled', 'fulfilled', 'cancelled'
        'requested_by', // user_id of the requester
        'fulfilled_liters', // liters already allocated to this request
        'deadline',
    ];
    public function area()
    {
        return $this->belongsTo(Area::class, 'area_id');
    }
    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }
}
