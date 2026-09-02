<?php

namespace App\Policies;

use App\Models\Signalement;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SignalementPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    // عرض جميع signalements
    public function viewAny(User $user)
    {
        return $user->role === 'super_admin';
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Signalement  $signalement
     * @return \Illuminate\Auth\Access\Response|bool
     */
    // عرض signalement واحد
    public function view(User $user, Signalement $signalement)
    {
        return $user->role === 'super_admin';
    }
    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    // غير Visitor يقدر ينشئ signalement
    public function create(User $user)
    {
        return $user->role === 'visiteur';
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Signalement  $signalement
     * @return \Illuminate\Auth\Access\Response|bool
     */
    // تعديل signalement
    public function update(User $user, Signalement $signalement)
    {
        return $user->role === 'super_admin';
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Signalement  $signalement
     * @return \Illuminate\Auth\Access\Response|bool
     */
    // حذف signalement
    public function delete(User $user, Signalement $signalement)
    {
        return $user->role === 'super_admin';
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Signalement  $signalement
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Signalement $signalement)
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Signalement  $signalement
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Signalement $signalement)
    {
        return false;
    }
}
