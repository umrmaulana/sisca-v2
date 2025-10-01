<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class P3kHistory extends Model
{
    use HasFactory;
    protected $fillable = ['p3k_id', 'npk', 'action', 'quantity', 'accident_id'];

    protected $casts = [
        'expired_at' => 'date',
    ];

    protected $table = 'p3k_histories';

    protected $guarded = [];

    public function p3k()
    {
        return $this->belongsTo(P3k::class, 'p3k_id', 'id');
    }

    public function accident()
    {
        return $this->belongsTo(P3kAccident::class);
    }
}
