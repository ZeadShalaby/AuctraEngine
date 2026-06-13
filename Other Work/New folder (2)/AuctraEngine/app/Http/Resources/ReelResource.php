<?php

namespace App\Http\Resources;

use App\Http\Resources\ReelUserResource;
use App\Models\Reels;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReelResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->item_type ?? 'reel',
            'title' => $this->title,
            'description' => $this->description,
            'video' => $this->getFirstMediaUrl('video'),
            'user' => new ReelUserResource($this->user),
            'interests' => $this->whenLoaded('interests'),
            'likes_count' => $this->item_type === 'ad'
                ? rand(100, 350)
                : ($this->likes_count ?? 0),
            'comments_count' => $this->item_type === 'ad'
                ? rand(50, 250)
                : ($this->comments_count ?? 0),
            'shares_count' => $this->item_type === 'ad'
                ? rand(20, 150)
                : ($this->shares_count ?? 0),
            'views_count' => $this->item_type === 'ad'
                ? rand(100, 350)
                : ($this->views_count ?? 0),
            'favorites_count' => $this->item_type === 'ad'
                ? rand(50, 250)
                : ($this->favorites_count ?? 0),
            'score' => $this->score ?? 0,
        ];
    }
}