<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    use HasFactory;

    protected $table = 'tm_areas';

    protected $fillable = [
        'area_name',
        'company_id',
        'mapping_picture',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function locations()
    {
        return $this->hasMany(Location::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
