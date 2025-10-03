<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterAccident extends Model
{
    use HasFactory;

    protected $table = 'tm_accident';

    public function accident()
    {
        return $this->hasMany(P3kAccident::class, 'accident_id');
    }
}
