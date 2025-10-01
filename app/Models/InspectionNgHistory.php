<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InspectionNgHistory extends Model
{
    use HasFactory;

    protected $table = 'tt_inspection_ng_histories';

    protected $fillable = [
        'original_inspection_id',
        'original_inspection_detail_id',
        'equipment_id',
        'checksheet_id',
        'user_id',
        'inspection_date',
        'picture',
        'status',
        'notes',
    ];

    protected $casts = [
        'inspection_date' => 'date',
    ];

    public function originalInspection()
    {
        return $this->belongsTo(Inspection::class, 'original_inspection_id');
    }

    public function originalInspectionDetail()
    {
        return $this->belongsTo(InspectionDetail::class, 'original_inspection_detail_id');
    }

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }

    public function checksheetTemplate()
    {
        return $this->belongsTo(ChecksheetTemplate::class, 'checksheet_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
