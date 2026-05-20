<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Station extends Model
{
    use SoftDeletes;

    protected $table = "stations";
    protected $fillable = [
        'user_id',
        'area_id',
        'station_code',
        'station_name',
        'address',
        'latitude',
        'longitude',
        'status',
    ];
}