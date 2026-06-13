<?php

namespace App\Http\Controllers\Api\Favourites;

use App\Http\Controllers\Controller;
use App\Http\Requests\Favourite\ToggleRequest;
use App\Repositories\Interfaces\FavouritesRepositoryInterface;

class FavouritesController extends Controller
{
    //
    public function __construct(protected FavouritesRepositoryInterface $favouritesRepository){}

    public function toggle(ToggleRequest $request)
    {
        $data = $request->validated();
        return successResponse(__('favourite.toggle'), $this->favouritesRepository->toggle($data));
    }

    public function myFavourites()
    {
        $my = $this->favouritesRepository->myFavourites();
        return successResponse(__('favourite.my'), $my,200);
    }
}
