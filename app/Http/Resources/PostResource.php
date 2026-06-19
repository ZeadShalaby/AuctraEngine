<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'type' => $this->item_type ?? 'post',
            'title' => $this->title,
            'content' => $this->content,

            'media' => $this->media->map(function ($m) {
                return [
                    'id' => $m->id,
                    'type' => str_contains($m->mime_type, 'video') ? 'video' : 'image',
                    'url' => $m->original_url,
                ];
            }),

            'stats' => [
                'likes' => $this->likes_count ?? rand(121, 505),
                'comments' => $this->comments_count ?? rand(21, 305),
                'shares' => $this->shares_count ?? rand(0, 10),
            ],

            'user' => [
                'id' => $this->user->id,
                'username' => $this->user->username,
                'name' => $this->user->full_name,
                'phonenumber' => $this->user->phone_number,
                // 'profile' => $this->user->userProfile,
                'media' => $this->user->getFirstMediaUrl('avatar') ?: asset('storage/images/default.png'),
                'details' => [
                    'rating' => round($this->user->reviews_avg_rating ?? 0, 1),
                    'reviews' => $this->user->reviews_count,
                    'sold' => $this->user->sold_items_count,
                ],
            ],


        ];
    }
}
