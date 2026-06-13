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
            'favoriteable_id' => $data['favoriteable_id'],
            'favoriteable_type' => $data['favoriteable_type'],
        ], 'favorite');
    }

    public function myFavourites($perPage = 12)
    {
        $favourites = $this->favourite
            ->where('user_id', auth()->id())
            ->with('favoriteable')
            ->paginate($perPage);


        $favourites->getCollection()->transform(function ($favourite) {

            $item = $favourite->favoriteable;

            return [
                'id' => $favourite->id,
                'type' => $favourite->favoriteable_type,
                'is_favorite' => true,
                'user' => [
                    'user_id' => $favourite->user_id,
                    'username' => $favourite->user?->username,
                    'fullname' => $favourite->user?->full_name,
                    'avatar' => $favourite->user?->getProfileImage(),
                ],
                'item' => [
                    'id' => $item?->id,
                    'title' => $item?->title,
                    'content' => $item?->content,
                    'likes_count' => $item?->likes_count,
                    'comments_count' => $item?->comments_count,
                    'shares_count' => $item?->shares_count,
                    // 'image' => $item?->getFirstMediaUrl('image'),
                    'video' => $item?->getFirstMediaUrl('video'),
                ],
                'created_at' => $favourite->created_at->diffForHumans(),
            ];
        });

        return $favourites;
    }
}