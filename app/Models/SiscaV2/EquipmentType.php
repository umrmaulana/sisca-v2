<?php

namespace App\Models\SiscaV2;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EquipmentType extends Model
{
    use HasFactory;

    protected $table = 'tm_equipment_types';

    protected $fillable = [
        'equipment_name',
        'equipment_type',
        'desc',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Accessor for name (use equipment_name)
    public function getNameAttribute()
    {
        return $this->equipment_name;
    }

    public function equipments()
    {
        return $this->hasMany(Equipment::class);
    }

    public function checksheetTemplates()
    {
        return $this->hasMany(ChecksheetTemplate::class);
    }
}
