<?php

namespace App\Models\SiscaV2;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    protected $table = 'tm_locations_new';

    protected $fillable = [
        'location_code',
        'plant_id',
        'area_id',
        'pos',
        'coordinate_x',
        'coordinate_y',
        'plant_coordinate_x',
        'plant_coordinate_y',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'coordinate_x' => 'decimal:6',
        'coordinate_y' => 'decimal:6',
        'plant_coordinate_x' => 'decimal:6',
        'plant_coordinate_y' => 'decimal:6',
    ];

    // Accessor for location_name (use location_code as display name)
    public function getLocationNameAttribute()
    {
        return $this->location_code;
    }

    public function plant()
    {
        return $this->belongsTo(Plant::class);
    }

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function equipments()
    {
        return $this->hasMany(Equipment::class);
    }
}
