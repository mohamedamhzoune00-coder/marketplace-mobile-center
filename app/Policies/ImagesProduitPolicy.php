<?php

namespace App\Policies;

use App\Models\ImagesProduit;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ImagesProduitPolicy
{
    use HandlesAuthorization;

    // أي مستخدم مسجل يقدر يشوف الصور
    public function viewAny(User $user)
    {
        return true;
    }

    // أي مستخدم مسجل يقدر يشوف صورة
    public function view(User $user, ImagesProduit $imagesProduit)
    {
        return true;
    }

    // غير vendeur و super_admin يقدرو يضيفو صور
    public function create(User $user)
    {
        return in_array($user->role, ['super_admin', 'vendeur']);
    }

    // غير مول المنتج أو super_admin يقدر يعدل الصورة
    public function update(User $user, ImagesProduit $imagesProduit)
    {
        return $user->role === 'super_admin'
            || $user->id === $imagesProduit->produit->boutique->user_id;
    }

    // غير مول المنتج أو super_admin يقدر يحذف الصورة
    public function delete(User $user, ImagesProduit $imagesProduit)
    {
        return $user->role === 'super_admin'
            || $user->id === $imagesProduit->produit->boutique->user_id;
    }

    public function restore(User $user, ImagesProduit $imagesProduit)
    {
        return false;
    }

    public function forceDelete(User $user, ImagesProduit $imagesProduit)
    {
        return false;
    }
}