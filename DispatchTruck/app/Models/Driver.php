<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    protected $table = "drivers";
    protected $fillable = [
        'user_id',
        'licensed_number',
        'status', // available, on-duty, off-duty
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function currentAssignment()
    {
        return $this->hasOne(TruckAssignment::class)->where('status', 'active')->latest('start_time');
    }

    public function assignments()
    {
        return $this->hasMany(TruckAssignment::class);
    }
}