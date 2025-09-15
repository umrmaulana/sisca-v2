<?php

namespace App\Models\SiscaV2;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'name',
        'npk',
        'role',
        'plant_id',
        'is_active',
        'email_verified_at',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function plant()
    {
        return $this->belongsTo(Plant::class);
    }

    public function inspections()
    {
        return $this->hasMany(Inspection::class);
    }

}
