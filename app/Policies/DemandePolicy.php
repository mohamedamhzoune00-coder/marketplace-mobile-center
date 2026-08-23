<?php

namespace App\Policies;

use App\Models\Demande;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DemandePolicy
{
    use HandlesAuthorization;

    // ghi vendeur o admin li 3ndhom l7a9 ychofo lista dyal talabat
    public function viewAny(User $user)
    {
        return in_array($user->role, ['super_admin', 'vendeur']);
    }

    // chkoun 3ndo l7a9 ychouf talab wa7ed
    public function view(User $user, Demande $demande)
    {
        if ($user->role === 'super_admin') return true;

        if ($user->role === 'vendeur') {
            // ghi ila had talab tabe3 l boutique dyalo
            return $user->boutique && $user->boutique->id === $demande->boutique_id;
        }

        // visiteur: ghi ila howa li dar had talab
        return $user->id === $demande->user_id;
    }

    // ghi visiteur li 3ndo l7a9 ydir talab jdid
    public function create(User $user)
    {
        return $user->role === 'visiteur';
    }

    // visiteur ye3del talab dyalo ghi ila mazal en_attente
    public function update(User $user, Demande $demande)
    {
        return $user->role === 'visiteur'
            && $user->id === $demande->user_id
            && $demande->statut === 'en_attente';
    }

    // nafs chi haja l delete
    public function delete(User $user, Demande $demande)
    {
        return $user->role === 'visiteur'
            && $user->id === $demande->user_id
            && $demande->statut === 'en_attente';
    }

    // ghi vendeur (mol l boutique) o admin y9dro yqbelo talab, o ghi ila mazal en_attente
    public function accept(User $user, Demande $demande)
    {
        if ($demande->statut !== 'en_attente') return false;
        if ($user->role === 'super_admin') return true;
        return $user->role === 'vendeur' && $user->boutique && $user->boutique->id === $demande->boutique_id;
    }

    // nafs qa3ida l refuse
    public function refuse(User $user, Demande $demande)
    {
        return $this->accept($user, $demande);
    }
}