<?php

namespace App\Policies;

use App\Models\HorairesBoutique;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;


class HoraireBoutiquePolicy
{
    use HandlesAuthorization;

    // عرض جميع المواعيد
    public function viewAny(User $user)
    {
        return true;
    }

    // عرض موعد واحد
    public function view(User $user, HorairesBoutique $horaireBoutique)
    {
        return true;
    }

    // إنشاء موعد
    public function create(User $user)
    {
        return $user->role === 'vendeur' || $user->role === 'super_admin';
    }

    // تعديل موعد
    public function update(User $user, HorairesBoutique $horaireBoutique)
    {
        if ($user->role === 'super_admin') {
            return true;
        }

        return $user->role === 'vendeur'
            && $horaireBoutique->boutique->user_id === $user->id;
    }

    // حذف موعد
    public function delete(User $user, HorairesBoutique $horaireBoutique)
    {
        if ($user->role === 'super_admin') {
            return true;
        }

        return $user->role === 'vendeur'
            && $horaireBoutique->boutique->user_id === $user->id;
    }
    public function restore(User $user, HorairesBoutique $horaireBoutique)
    {
        return false;
    }

    public function forceDelete(User $user, HorairesBoutique $horaireBoutique)
    {
        return false;
    }
}
