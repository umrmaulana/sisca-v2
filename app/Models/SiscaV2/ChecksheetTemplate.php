<?php

namespace App\Models\SiscaV2;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ChecksheetTemplate extends Model
{
    use HasFactory;

    protected $table = 'tm_checksheet_templates';

    protected $fillable = [
        'equipment_type_id',
        'order_number',
        'item_name',
        'standar_condition',
        'standar_picture',
        'is_active',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function equipmentType()
    {
        return $this->belongsTo(EquipmentType::class);
    }

    public function inspectionDetails()
    {
        return $this->hasMany(InspectionDetail::class, 'checksheet_id');
    }

    /**
     * Get the user who created this template
     */
    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    /**
     * Get the user who last updated this template
     */
    public function updater()
    {
        return $this->belongsTo(\App\Models\User::class, 'updated_by');
    }

    /**
     * Scope active templates
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get next order number for specific equipment type
     */
    public static function getNextOrderNumber($equipmentTypeId)
    {
        $maxOrder = self::where('equipment_type_id', $equipmentTypeId)->max('order_number');
        return $maxOrder ? $maxOrder + 1 : 1;
    }

    /**
     * Check if order number exists for equipment type
     */
    public static function orderExists($equipmentTypeId, $orderNumber, $excludeId = null)
    {
        $query = self::where('equipment_type_id', $equipmentTypeId)
            ->where('order_number', $orderNumber);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }
}