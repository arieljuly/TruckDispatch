<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DistanceMatrix extends Model
{
    protected $table = "distance_matrix";

    protected $fillable = [
        "from_area_id",
        "to_area_id",
        "distance",
        "travel_time",
    ];
    public function fromArea()
    {
        return $this->belongsTo(Area::class, 'from_area_id');
    }   
    public function toArea()
    {
        return $this->belongsTo(Area::class, 'to_area_id');
    }
}
