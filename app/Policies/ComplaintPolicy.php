<?php

namespace App\Policies;

use App\Enums\UserType;
use App\Models\Complaint;
use App\Models\User;
use Illuminate\Auth\Access\Response;


class ComplaintPolicy
{

    // Admin: كل الشكاوي - User: شكاويه فقط
    public function viewAny(User $user): bool
    {
        return isAdmin($user) || isUser($user);
    }

    // view single complaint
    public function view(User $user, Complaint $complaint): bool
    {
        return isAdmin($user) || isOwner($user, $complaint);
    }

    // create complaint (أي user طبيعي)
    public function create(User $user): bool
    {
        return isUser($user) || isAdmin($user);
    }

    // user يقدر يعدل شكواه هو فقط (لو محتاج)
    public function update(User $user, Complaint $complaint): bool
    {
        return isAdmin($user) || isOwner($user, $complaint);
    }

    // delete: admin أو صاحب الشكوى
    public function delete(User $user, Complaint $complaint): bool
    {
        return isAdmin($user) || isOwner($user, $complaint);
    }

    // admin فقط
    public function changeStatus(User $user, Complaint $complaint): bool
    {
        return isAdmin($user);
    }

    public function restore(User $user, Complaint $complaint): bool
    {
        return isAdmin($user);
    }

    public function forceDelete(User $user, Complaint $complaint): bool
    {
        return isAdmin($user);
    }
}