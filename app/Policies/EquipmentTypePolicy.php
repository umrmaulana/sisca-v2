<?php

namespace App\Policies;

use App\Models\EquipmentType;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class EquipmentTypePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        return in_array($user->role, ['Admin', 'Supervisor', 'Management']);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\\EquipmentType  $equipmentType
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, EquipmentType $equipmentType)
    {
        return in_array($user->role, ['Admin', 'Management', 'Supervisor']);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return in_array($user->role, ['Admin']);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\\EquipmentType  $equipmentType
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, EquipmentType $equipmentType)
    {
        return in_array($user->role, ['Admin']);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\\EquipmentType  $equipmentType
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, EquipmentType $equipmentType)
    {
        return in_array($user->role, ['Admin']);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\\EquipmentType  $equipmentType
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, EquipmentType $equipmentType)
    {
        return $user->role === 'Admin';
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\\EquipmentType  $equipmentType
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, EquipmentType $equipmentType)
    {
        return $user->role === 'Admin';
    }
}
