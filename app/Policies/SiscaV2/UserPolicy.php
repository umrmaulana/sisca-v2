<?php

namespace App\Policies\SiscaV2;

use App\Models\SiscaV2\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\SiscaV2\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        return $user->role === 'Admin';
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\SiscaV2\User  $user
     * @param  \App\Models\SiscaV2\User  $model
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, User $model)
    {
        return $user->role === 'Admin';
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\SiscaV2\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $user->role === 'Admin';
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\SiscaV2\User  $user
     * @param  \App\Models\SiscaV2\User  $model
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, User $model)
    {
        return $user->role === 'Admin';
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\SiscaV2\User  $user
     * @param  \App\Models\SiscaV2\User  $model
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, User $model)
    {
        // Admin can't delete themselves
        if ($user->id === $model->id) {
            return false;
        }

        return $user->role === 'Admin';
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\SiscaV2\User  $user
     * @param  \App\Models\SiscaV2\User  $model
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, User $model)
    {
        return $user->role === 'Admin';
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\SiscaV2\User  $user
     * @param  \App\Models\SiscaV2\User  $model
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, User $model)
    {
        return $user->role === 'Admin';
    }
}
