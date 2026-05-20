<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Area extends Model
{
    use SoftDeletes;

    protected $table = "areas";
    protected $fillable = [
        'area_name',
        'area_code',
        'latitude',
        'longitude',
        'status',
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
    ];
    public function stations(): HasMany
    {
        return $this->hasMany(Station::class);
    }

    public function getRequiredLitersAttribute(): float
    {
        return $this->stations()->sum('required_liters');
    }
}