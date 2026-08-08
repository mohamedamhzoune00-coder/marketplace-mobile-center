<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CategoryPolicy
{
    use HandlesAuthorization;

    // عرض جميع التصنيفات
    public function viewAny(User $user)
    {
        return true;
    }

    // عرض تصنيف واحد
    public function view(User $user, Category $category)
    {
        return true;
    }

    // إنشاء تصنيف
    public function create(User $user)
    {
        return $user->role === 'super_admin';
    }

    // تعديل تصنيف
    public function update(User $user, Category $category)
    {
        return $user->role === 'super_admin';
    }

    // حذف تصنيف
    public function delete(User $user, Category $category)
    {
        return $user->role === 'super_admin';
    }

    public function restore(User $user, Category $category)
    {
        return false;
    }

    public function forceDelete(User $user, Category $category)
    {
        return false;
    }
}