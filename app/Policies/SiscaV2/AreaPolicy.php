<?php

namespace App\Policies\SiscaV2;

use App\Models\SiscaV2\Area;
use App\Models\SiscaV2\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AreaPolicy
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
     * @param  \App\Models\SiscaV2\Area  $area
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Area $area)
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
     * @param  \App\Models\SiscaV2\Area  $area
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Area $area)
    {
        return in_array($user->role, ['Admin']);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\SiscaV2\Area  $area
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Area $area)
    {
        return in_array($user->role, ['Admin']);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\SiscaV2\Area  $area
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Area $area)
    {
        // Only Admin can restore
        return $user->role === 'Admin';
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\SiscaV2\Area  $area
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Area $area)
    {
        // Only Admin can force delete
        return $user->role === 'Admin';
    }
}
