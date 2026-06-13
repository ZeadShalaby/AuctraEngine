<?php

namespace App\Repositories\Eloquent;

use App\Models\User;

class AuthRepository
{
    public function __construct(protected User $user){}
    public function createUser(array $data)
    {
        return $this->user::create($data);
    }

    public function findUser($id)
    {
        return $this->user::find($id);
    }

    public function findByEmail($email)
    {
        return $this->user::where('email', $email)->first();
    }

    public function updateUser($user, array $data)
    {
        return $this->user->update($data);
    }
}