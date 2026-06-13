<?php
namespace App\Policies;

use App\Enums\UserType;
use App\Models\Post;
use App\Models\User;

class PostPolicy
{
    

    /**
     * أي حد يقدر يشوف البوستات
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * أي حد يقدر يشوف بوست واحد
     */
    public function view(User $user, Post $post): bool
    {
        return true;
    }

    /**
     * إنشاء بوست: user أو admin
     */
    public function create(User $user): bool
    {
        return isUser($user) || isAdmin($user);
    }

    /**
     * تعديل: صاحب البوست فقط أو admin
     */
    public function update(User $user, Post $post): bool
    {
        return isOwner($user, $post) || isAdmin($user);
    }

    /**
     * حذف: صاحب البوست أو admin
     */
    public function delete(User $user, Post $post): bool
    {
        return isOwner($user, $post)|| isAdmin($user);
    }

    public function restore(User $user, Post $post): bool
    {
        return isAdmin($user);
    }

    public function forceDelete(User $user, Post $post): bool
    {
        return isAdmin($user);
    }
}