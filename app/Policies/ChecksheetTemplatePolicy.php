<?php

namespace App\Policies;

use App\Models\ChecksheetTemplate;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ChecksheetTemplatePolicy
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
     * @param  \App\Models\ChecksheetTemplate  $checksheetTemplate
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, ChecksheetTemplate $checksheetTemplate)
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
     * @param  \App\Models\ChecksheetTemplate  $checksheetTemplate
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, ChecksheetTemplate $checksheetTemplate)
    {
        return in_array($user->role, ['Admin']);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ChecksheetTemplate  $checksheetTemplate
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, ChecksheetTemplate $checksheetTemplate)
    {
        return in_array($user->role, ['Admin']);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ChecksheetTemplate  $checksheetTemplate
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, ChecksheetTemplate $checksheetTemplate)
    {
        // Only Admin can restore
        return $user->role === 'Admin';
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ChecksheetTemplate  $checksheetTemplate
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, ChecksheetTemplate $checksheetTemplate)
    {
        // Only Admin can force delete
        return $user->role === 'Admin';
    }
}
