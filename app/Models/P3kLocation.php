<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class P3kLocation extends Model
{
    use HasFactory;

    protected $fillable = ['location'];

    protected $table = 'p3k_location';

    protected $guarded = [];

    public function p3k()
    {
        return $this->hasMany(P3k::class);
    }

    public function accident()
    {
        return $this->hasMany(P3kAccident::class, 'location_id');
    }

}
