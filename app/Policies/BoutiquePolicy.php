<?php

namespace App\Policies;

use App\Models\Boutique;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BoutiquePolicy
{
    use HandlesAuthorization;

    // أي واحد يقدر يشوف البوتيكات
    public function viewAny(User $user)
    {
        return true;
    }

    // أي واحد يقدر يشوف Boutique وحدة
    public function view(User $user, Boutique $boutique)
    {
        return true;
    }

    // غير vendeur و super_admin يقدرو ينشئو Boutique
    public function create(User $user)
    {
        return in_array($user->role, ['super_admin', 'vendeur']);
    }

    // غير مول البوتيك أو super_admin يقدر يعدل
    public function update(User $user, Boutique $boutique)
    {
        return $user->role === 'super_admin'
            || $user->id === $boutique->user_id;
    }

    // غير super_admin يقدر يحذف
    public function delete(User $user, Boutique $boutique)
    {
        return $user->role === 'super_admin';
    }
}