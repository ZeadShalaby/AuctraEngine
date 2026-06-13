<?php

namespace App\Repositories\Interfaces;

interface FavouritesRepositoryInterface
{
    public function toggle(array $data);
}