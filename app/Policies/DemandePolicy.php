<?php

namespace App\Policies;

use App\Models\Demande;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DemandePolicy
{
    use HandlesAuthorization;

    // أي مستخدم مسجل يقدر يشوف الطلبات ديالو
    public function viewAny(User $user)
    {
        return true;
    }

    // Super Admin أو صاحب الطلب
    public function view(User $user, Demande $demande)
    {
        return $user->role === 'super_admin'
            || $user->id === $demande->user_id;
    }

    // غير Visitor يقدر ينشئ طلب شراء
    public function create(User $user)
    {
        return $user->role === 'visiteur';
    }

    // غير صاحب الطلب يقدر يعدلو
    public function update(User $user, Demande $demande)
    {
        return $user->id === $demande->user_id;
    }

    // غير صاحب الطلب أو Super Admin يقدر يحذف
    public function delete(User $user, Demande $demande)
    {
        return $user->role === 'super_admin'
            || $user->id === $demande->user_id;
    }

    public function restore(User $user, Demande $demande)
    {
        return false;
    }

    public function forceDelete(User $user, Demande $demande)
    {
        return false;
    }
}