<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $table = 'tm_companies';

    protected $fillable = [
        'company_name',
        'company_mapping_picture',
        'company_description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function locations()
    {
        return $this->hasMany(Location::class);
    }

    public function areas()
    {
        return $this->hasMany(Area::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
