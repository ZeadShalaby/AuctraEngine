<?php
namespace App\Policies;

use App\Enums\UserType;
use App\Models\Category;
use App\Models\User;

class CategoryPolicy
{

    /**
     * أي حد يقدر يشوف الكاتيجوريز
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * أي حد يقدر يشوف كاتيجوري واحدة
     */
    public function view(User $user, Category $category): bool
    {
        return true;
    }

    /**
     * إنشاء: admin فقط
     */
    public function create(User $user): bool
    {
        return isAdmin($user);
    }

    /**
     * تعديل: admin فقط
     */
    public function update(User $user, Category $category): bool
    {
        return isAdmin($user);
    }

    /**
     * حذف: admin فقط
     */
    public function delete(User $user, Category $category): bool
    {
        return isAdmin($user);
    }

    public function restore(User $user, Category $category): bool
    {
        return isAdmin($user);
    }

    public function forceDelete(User $user, Category $category): bool
    {
        return isAdmin($user);
    }
}