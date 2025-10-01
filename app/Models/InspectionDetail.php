<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InspectionDetail extends Model
{
    use HasFactory;

    protected $table = 'tt_inspection_details';

    protected $fillable = [
        'inspection_id',
        'checksheet_id',
        'picture',
        'status',
    ];

    public function inspection()
    {
        return $this->belongsTo(Inspection::class);
    }

    public function checksheetTemplate()
    {
        return $this->belongsTo(ChecksheetTemplate::class, 'checksheet_id');
    }

    public function ngHistories()
    {
        return $this->hasMany(InspectionNgHistory::class, 'original_inspection_detail_id');
    }
}
