<?php

namespace App\Policies;

use App\Models\Demande;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DemandePolicy
{
    use HandlesAuthorization;

    // عرض لائحة الطلبات:
    // Vendeur يقدر يشوف الطلبات ديال boutique ديالو
    // Super Admin يقدر يشوف جميع الطلبات
    // Visiteur ما يقدرش يشوف لائحة الطلبات كاملة
    public function viewAny(User $user)
    {
        return $user->role === 'vendeur'
            || $user->role === 'super_admin';
    }

    // عرض طلب واحد:
    // Super Admin يقدر يشوف أي demande
    // Visiteur يقدر يشوف غير demande ديالو
    // Vendeur يقدر يشوف غير الطلبات ديال boutique ديالو
    public function view(User $user, Demande $demande)
    {
        return $user->role === 'super_admin'
            || $user->id === $demande->user_id
            || (
                $user->role === 'vendeur'
                && $user->boutique
                && $user->boutique->id === $demande->boutique_id
            );
    }

    // إنشاء طلب شراء:
    // غير Visiteur هو اللي يقدر يدير Demande
    public function create(User $user)
    {
        return $user->role === 'visiteur';
    }

    // تعديل الطلب:
    // Visiteur يقدر يعدل غير الطلب ديالو
    // وضروري الطلب يكون مازال en_attente
    // منين vendeur يقبلو أو يرفضو، ما يبقاش visitor يقدر يبدلو
    public function update(User $user, Demande $demande)
    {
        return $user->role === 'visiteur'
            && $user->id === $demande->user_id
            && $demande->statut === 'en_attente';
    }

    // حذف الطلب:
    // Visiteur يقدر يحذف غير الطلب ديالو
    // وضروري يكون مازال en_attente
    public function delete(User $user, Demande $demande)
    {
        return $user->role === 'visiteur'
            && $user->id === $demande->user_id
            && $demande->statut === 'en_attente';
    }

    // استرجاع الطلب المحذوف
    // حالياً ما عندناش هاد fonctionnalité
    public function restore(User $user, Demande $demande)
    {
        return false;
    }

    // حذف نهائي
    // حالياً ما بغيناش نستعملوه
    public function forceDelete(User $user, Demande $demande)
    {
        return false;
    }
}