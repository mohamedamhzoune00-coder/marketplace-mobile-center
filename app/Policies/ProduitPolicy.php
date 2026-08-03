<?php

namespace App\Policies;

use App\Models\Produit;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProduitPolicy
{
    use HandlesAuthorization;

    // أي واحد يقدر يشوف المنتجات
    public function viewAny(User $user)
    {
        return true;
    }

    // أي واحد يقدر يشوف منتوج واحد
    public function view(User $user, Produit $produit)
    {
        return true;
    }

    // غير vendeur و super_admin يقدرو يزيدو منتوج
    public function create(User $user)
    {
        return in_array($user->role, ['super_admin', 'vendeur']);
    }

    // غير مول البوتيك ديال المنتوج أو super_admin يقدر يعدل
    public function update(User $user, Produit $produit)
    {
        return $user->role === 'super_admin'
            || $user->id === $produit->boutique->user_id;
    }

    // غير super_admin يقدر يحذف
    public function delete(User $user, Produit $produit)
    {
        return $user->role === 'super_admin';
    }
}