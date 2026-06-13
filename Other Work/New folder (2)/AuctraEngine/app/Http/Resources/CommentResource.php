<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'content' => $this->content,
            'created_at' => $this->created_at->diffForHumans(),

            'user' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user?->id,
                    'username' => $this->user?->username,
                    'full_name' => $this->user?->full_name,
                    'phone' => $this->user?->phone_number,
                    'avatar' => $this->user?->getProfileImage(),
                ];
            }),

            'commentable_type' => class_basename($this->commentable_type),

            'commentable' => $this->whenLoaded('commentable', function () {

                return match (class_basename($this->commentable_type)) {

                    'Reels' => [
                        'id' => $this->commentable?->id,
                        'title' => $this->commentable?->title,
                        'description' => $this->commentable?->description,
                        'likes_count' => $this->commentable?->likes_count,
                        'comments_count' => $this->commentable?->comments_count,
                        'shares_count' => $this->commentable?->shares_count,
                        'views_count' => $this->commentable?->views_count,
                        'video' => $this->commentable?->getFirstMediaUrl('video'),
                    ],

                    'Post' => [
                        'id' => $this->commentable?->id,
                        'title' => $this->commentable?->title,
                        'content' => $this->commentable?->content,
                        'likes_count' => $this->commentable?->likes_count,
                        'comments_count' => $this->commentable?->comments_count,
                        'shares_count' => $this->commentable?->shares_count,
                        'image' => $this->commentable?->image,
                        'video' => $this->commentable?->video,
                    ],

                    default => null,
                };
            }),
        ];
    }
}