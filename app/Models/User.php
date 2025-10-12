<?php

namespace App\Models;

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
        'company_id',
        'module_permissions',
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
        'module_permissions' => 'array',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function inspections()
    {
        return $this->hasMany(Inspection::class);
    }

    /**
     * Check if user has access to a specific module
     */
    public function hasModuleAccess($module)
    {
        // Admin always has access to all modules
        if ($this->role === 'Admin') {
            return true;
        }

        // If no permissions set, return false
        if (!$this->module_permissions) {
            return false;
        }

        return in_array($module, $this->module_permissions);
    }

    /**
     * Get available modules for this user
     */
    public function getAvailableModules()
    {
        if ($this->role === 'Admin') {
            return ['checksheet', 'p3k', 'user_management'];
        }

        return $this->module_permissions ?? [];
    }

    /**
     * Set module permissions
     */
    public function setModulePermissions(array $modules)
    {
        $this->module_permissions = $modules;
        $this->save();
    }

}
