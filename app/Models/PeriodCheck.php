<?php

namespace App\Models;

use App\Models\Equipment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeriodCheck extends Model
{
    use HasFactory;

    protected $table = 'tm_period_checks';

    protected $fillable = [
        'period_check',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function equipments()
    {
        return $this->hasMany(Equipment::class);
    }
}
