<?php

namespace App\Models\SiscaV2;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    use HasFactory;

    protected $table = 'tm_equipments';

    protected $fillable = [
        'equipment_code',
        'equipment_type_id',
        'location_id',
        'desc',
        'qrcode',
        'period_check_id',
        'is_active',
    ];

    // Accessor for code (use equipment_code)
    public function getCodeAttribute()
    {
        return $this->equipment_code;
    }

    // Accessor for name (use desc)
    public function getNameAttribute()
    {
        return $this->desc;
    }

    public function equipmentType()
    {
        return $this->belongsTo(EquipmentType::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function periodCheck()
    {
        return $this->belongsTo(PeriodCheck::class);
    }

    public function inspections()
    {
        return $this->hasMany(Inspection::class);
    }

    // Get the latest inspection
    public function getLatestInspectionAttribute()
    {
        return $this->inspections()->with('user')->latest()->first();
    }

    // Get the last inspector information
    public function getLastInspectorAttribute()
    {
        $latestInspection = $this->latestInspection;
        return $latestInspection ? $latestInspection->user : null;
    }

    // Get formatted inspector info
    public function getInspectorInfoAttribute()
    {
        $inspector = $this->lastInspector;
        if ($inspector) {
            return "{$inspector->name} (NPK: {$inspector->npk})";
        }
        return 'Not inspected yet';
    }

    // Check if equipment was inspected in specific month/year
    public function wasInspectedIn($year, $month)
    {
        return $this->inspections()
            ->whereYear('inspection_date', $year)
            ->whereMonth('inspection_date', $month)
            ->exists();
    }

    // Get inspection count for specific month/year
    public function getInspectionCountFor($year, $month)
    {
        return $this->inspections()
            ->whereYear('inspection_date', $year)
            ->whereMonth('inspection_date', $month)
            ->count();
    }

    // Get latest inspection for specific month/year
    public function getLatestInspectionFor($year, $month)
    {
        return $this->inspections()
            ->whereYear('inspection_date', $year)
            ->whereMonth('inspection_date', $month)
            ->latest('inspection_date')
            ->first();
    }
}
