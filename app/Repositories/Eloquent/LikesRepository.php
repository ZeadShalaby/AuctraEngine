<?php

namespace App\Repositories\Eloquent;

use App\Models\Like;
use App\Models\Post;
use App\Repositories\Interfaces\LikesRepositoryInterface;

class LikesRepository implements LikesRepositoryInterface
{

    public function __construct(protected Like $likes)
    {
    }

    public function toggle(array $data)
    {
        return toggleInteraction(Like::class, [
            'likeable_id' => $data['likeable_id'],
            'likeable_type' => $data['likeable_type'],
        ], 'like');
    }

    public function get(array $data, $perPage = 15)
    {
        $likes = $this->likes
            ->where('likeable_id', $data['likeable_id'])
            ->where('likeable_type', $data['likeable_type'])
            ->with([
                'user:id,username,first_name,last_name'
            ])
            ->paginate($perPage);

        $likes->getCollection()->transform(function ($like) {

            return [
                'id' => $like->id,
                'created_at' => $like->created_at->diffForHumans(),
                'is_like' => true,
                'user' => [
                    'id' => $like->user->id,
                    'username' => $like->user->username,
                    'name' => $like->user->first_name . ' ' . $like->user->last_name,
                    'image' => $like->user->getFirstMediaUrl('avatar')
                        ?: asset('storage/default.png'),
                ],
                'type' => $like->type ?? 'like',
            ];
        });

        return $likes;
    }
}