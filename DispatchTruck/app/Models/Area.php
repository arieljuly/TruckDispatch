<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Area extends Model
{
    protected $table = "areas";
    protected $fillable = [
        'area_name',
        'required_liters',
        'latitude',
        'longitude',
    ];  
}
