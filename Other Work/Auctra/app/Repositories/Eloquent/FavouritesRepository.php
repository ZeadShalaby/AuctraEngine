<?php

namespace App\Repositories\Eloquent;

use App\Models\Favourite;
use App\Repositories\Interfaces\FavouritesRepositoryInterface;


class FavouritesRepository implements FavouritesRepositoryInterface
{

    public function __construct(protected Favourite $favourite)
    {
    }


    public function toggle(array $data)
    {
        return toggleInteraction(Favourite::class, [
            'favoritable_id' => $data['favoritable_id'],
            'favoritable_type' => $data['favoritable_type'],
        ], 'favorite');
    }
}