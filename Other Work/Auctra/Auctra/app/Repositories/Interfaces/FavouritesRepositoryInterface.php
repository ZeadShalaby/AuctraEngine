<?php

namespace App\Repositories\Interfaces;

interface FavouritesRepositoryInterface
{
    public function toggle(array $data);
    public function myFavourites($perPage = 12);
}