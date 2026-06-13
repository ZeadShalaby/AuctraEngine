<?php

namespace App\Repositories\Interfaces;

interface LikesRepositoryInterface
{
    public function toggle(array $data);
    public function get(array $data , $perPage = 15);
}