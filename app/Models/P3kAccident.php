<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class P3kAccident extends Model
{
    use HasFactory;

    protected $fillable = ['reported_by', 'location_id', 'accident_id', 'accident_other', 'department_id', 'victim_npk', 'victim_name'];

    protected $table = 'tt_accidents';

    public function location()
    {
        return $this->belongsTo(P3kLocation::class, 'location_id');
    }

    public function history()
    {
        return $this->hasMany(P3kHistory::class, 'p3k_history_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function masterAccident()
    {
        return $this->belongsTo(MasterAccident::class, 'accident_id');
    }
}

