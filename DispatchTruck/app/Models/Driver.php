<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    protected $table = "drivers";
    protected $fillable = [
        'user_id',
        'license_number',
        'status', // available, on-duty, off-duty
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
