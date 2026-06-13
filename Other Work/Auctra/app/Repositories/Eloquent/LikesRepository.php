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
        return $this->likes->where('likeable_id', $data['likeable_id'])
            ->where('likeable_type', $data['likeable_type'])
            ->paginate($perPage);
    }
}