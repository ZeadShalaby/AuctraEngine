<?php

namespace App\Repositories\Eloquent;

use App\Models\Post;
use App\Repositories\Interfaces\PostsRepositoryInterface;
use Exception;

class PostsRepository implements PostsRepositoryInterface
{

    public function __construct(protected Post $post)
    {
    }

    /**
     * Base query with relations
     */
    private function query()
    {
        return $this->post->with([
            'user',
            'likes',
            'comments',
            'shares',
            'ads',
            'reports',
        ])->latest();
    }

    /**
     * Get all posts
     */
    public function all($perPage = 15)
    {
        $posts = $this->query()->paginate($perPage);
        $posts->getCollection()->transform(function ($post) {
            $post->item_type = 'post';
            return $post;
        });

        return $posts;
    }

    /**
     * Find single post
     */
    public function find($id)
    {
        return $this->query()->findOrFail($id);
    }

    /**
     * Create post
     */
    public function create(array $data)
    {

        $mediaData = [
            'image' => $data['image'] ?? null,
            'video' => $data['video'] ?? null,
        ];

        $data = array_diff_key($data, array_flip(['image', 'video']));
        $data['user_id'] = auth()->id();
        $post = $this->post->create($data);

        addMediaIfExists($post, $mediaData, 'image');
        addMediaIfExists($post, $mediaData, 'video');

        return $post;
    }

    /**
     * Update post
     */
    public function update($id, array $data)
    {

        $post = $this->post->findOrFail($id);
        $mediaData = [
            'image' => $data['image'] ?? null,
            'video' => $data['video'] ?? null,
        ];

        $data = array_diff_key($data, array_flip(['image', 'video']));

        $post->update($data);

        addMediaIfExists($post, $mediaData, 'image');
        addMediaIfExists($post, $mediaData, 'video');

        return $post;

    }

    /**
     * Delete post
     */
    public function delete($id)
    {
        $post = $this->post->findOrFail($id);
        $post->delete();
        return !$post;
    }

    /**
     * Search posts
     */
    public function search($query, $perPage = 10)
    {
        return $this->query()
            ->whereFullText(['title', 'content'], $query)
            ->paginate($perPage);
    }

    /**
     * Get user posts
     */
    public function userPosts($userId, $perPage = 10)
    {
        return $this->query()
            ->where('user_id', $userId)
            ->paginate($perPage);
    }

    /**
     * Get my posts
     */
    public function myPosts($perPage = 10)
    {
        return $this->query()
            ->where('user_id', auth()->id())
            ->paginate($perPage);
    }
}