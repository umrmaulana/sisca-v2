<?php

namespace App\Models\SiscaV2;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inspection extends Model
{
    use HasFactory;

    protected $table = 'tt_inspections';

    protected $fillable = [
        'user_id',
        'equipment_id',
        'inspection_date',
        'notes',
        'status',
        'approved_by',
        'approved_at',
        'approval_notes',
    ];

    protected $casts = [
        'inspection_date' => 'date',
        'approved_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }

    public function details()
    {
        return $this->hasMany(InspectionDetail::class);
    }


    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function ngHistories()
    {
        return $this->hasMany(InspectionNgHistory::class, 'original_inspection_id');
    }

    public function isApproved()
    {
        return $this->status === 'approved';
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    public function canBeUpdated()
    {
        return $this->status !== 'approved';
    }

    public function canBeApproved()
    {
        return $this->status === 'pending';
    }

    public function hasNgItems()
    {
        return $this->details()->where('status', 'NG')->count() > 0;
    }

    public function getNgItemsCount()
    {
        return $this->details()->where('status', 'NG')->count();
    }

    public function getOkItemsCount()
    {
        return $this->details()->where('status', 'OK')->count();
    }

    public function getTotalItemsCount()
    {
        return $this->details()->count();
    }

    public function getCompletionPercentage()
    {
        $total = $this->getTotalItemsCount();
        if ($total === 0) {
            return 0;
        }
        return round(($this->getOkItemsCount() / $total) * 100, 2);
    }
}
