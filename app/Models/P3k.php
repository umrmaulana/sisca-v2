<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class P3k extends Model
{
    use HasFactory;

    protected $fillable = ['item', 'location_id', 'tag_number', 'expired_at', 'standard_stock', 'actual_stock'];


    protected $table = 'tm_p3k';

    protected $casts = [
        'expired_at' => 'date',
    ];

    protected $guarded = [];

    public function history()
    {
        return $this->hasMany(P3kHistory::class);

    }

    public function location()
    {
        return $this->belongsTo(P3kLocation::class);
    }
}
