<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $table = 'tm_departments';

    public function accident()
    {
        return $this->hasMany(P3kAccident::class, 'department_id');
    }
}

