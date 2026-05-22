<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FuelType extends Model
{
    use SoftDeletes;

    protected $table = "fuel_types";
    protected $fillable = [
        'fuel_code',
        'fuel_name',
        'status',
    ];

    // Status constants
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';

    // Automatically update status when soft deleting/restoring
    protected static function booted()
    {
        static::deleting(function ($fuelType) {
            $fuelType->status = self::STATUS_INACTIVE;
            $fuelType->save();
        });

        static::restoring(function ($fuelType) {
            $fuelType->status = self::STATUS_ACTIVE;
        });
    }

    // Accessor for status badge color
    public function getStatusColorAttribute()
    {
        if ($this->trashed()) {
            return 'bg-red-100 text-red-800';
        }

        return match ($this->status) {
            'active' => 'bg-green-100 text-green-800',
            'inactive' => 'bg-yellow-100 text-yellow-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    // Scope for active fuel types
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }
}