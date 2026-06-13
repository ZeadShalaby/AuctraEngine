<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;

class CommentPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Comment $comment): bool
    {
       return isAdmin($user) || isOwner($user, $comment);
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Comment $comment): bool
    {
        return isAdmin($user) || isOwner($user, $comment);
    }

    public function delete(User $user, Comment $comment): bool
    {
        return isAdmin($user) || isOwner($user, $comment);
    }

    public function restore(User $user, Comment $comment): bool
    {
        return isAdmin($user);
    }

    public function forceDelete(User $user, Comment $comment): bool
    {
        return isAdmin($user);
    }
}