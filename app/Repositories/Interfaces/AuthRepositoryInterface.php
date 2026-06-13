<?php
namespace App\Repositories\Interfaces;


interface AuthRepositoryInterface
{
    public function createUser(array $data);
    public function findUser(int $id);
    public function findByEmail(string $email);
    public function updateUser($user, array $data);
    public function completeProfile($user, array $data);
}
