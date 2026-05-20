<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
        'required_liters',
        'status',
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'required_liters' => 'decimal:2',
    ];

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }
    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}