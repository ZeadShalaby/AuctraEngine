<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuctionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'title' => $this->title,
            'description' => $this->description,

            'location' => $this->location,

            'condition' => $this->condition,
            'status' => $this->status,

            'start_price' => (float) $this->start_price,
            'current_price' => (float) $this->current_price,
            'buy_now_price' => (float) $this->buy_now_price,
            'min_bid_increment' => (float) $this->min_bid_increment,

            'views' => $this->views,
            'bids_count' => $this->bids_count,

            'start_at' => $this->start_at,
            'end_at' => $this->end_at,

            //? Helper Fields
            'can_buy_now' => $this->buy_now_price > 0,

            'is_live' => $this->status === 'active'
                && now()->between($this->start_at, $this->end_at),

            'is_ended' => now()->greaterThan($this->end_at),

            'is_ending_soon' => now()->lt($this->end_at)
                && now()->diffInHours($this->end_at) <= 24,

            'remaining_seconds' => now()->lt($this->end_at)
                ? now()->diffInSeconds($this->end_at)
                : 0,

            'remaining_time' => now()->lt($this->end_at)
                ? Carbon::parse($this->end_at)->diffForHumans(now(), [
                    'parts' => 2,
                    'short' => true,
                    'syntax' => Carbon::DIFF_RELATIVE_TO_NOW,
                ])
                : 'Ended',
            'images' => $this->getMedia('images')->map(fn($media) => $media->getUrl()),
            'videos' => $this->getMedia('videos')->map(fn($media) => $media->getUrl()),

            'user' => [
                'id' => $this->user?->id,
                'name' => $this->user?->full_name,
                'username' => $this->user?->username,
                'image' => $this->user?->getProfileImage(),
            ],

            'category' => [
                'id' => $this->category?->id,
                'name' => $this->category?->name,
            ],

            'sub_category' => [
                'id' => $this->subCategory?->id,
                'name' => $this->subCategory?->name,
                'image' => $this->subCategory?->image,
            ],

            'winner' => $this->when(
                $this->winner,
                [
                    'id' => $this->winner?->id,
                    'name' => $this->winner?->full_name,
                    'username' => $this->winner?->username,
                    'image' => $this->user?->getProfileImage(),

                ]
            ),

            'created_at' => $this->created_at,
        ];
    }
}
