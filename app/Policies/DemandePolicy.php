<?php

namespace App\Policies;

use App\Models\Demande;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DemandePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->role === 'vendeur' || $user->role === 'super_admin';
    }

    public function view(User $user, Demande $demande)
    {
        return $user->role === 'super_admin'
            || ($user->role === 'vendeur' && $user->boutique && $user->boutique->id === $demande->boutique_id);
    }

    // vendeur/admin فقط كيبدلو الحالة (accepter/refuser) — الزائر ما عندوش حساب باش يعدل
    public function update(User $user, Demande $demande)
    {
        return $user->role === 'super_admin'
            || ($user->role === 'vendeur' && $user->boutique && $user->boutique->id === $demande->boutique_id);
    }

    public function delete(User $user, Demande $demande)
    {
        return $user->role === 'super_admin';
    }
}