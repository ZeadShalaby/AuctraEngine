<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuctionPromotionFilterResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'price' => $this->price,
            'starts_at' => $this->starts_at,
            'expires_at' => $this->expires_at,
            'status' => $this->status,

            'package' => [
                'id' => $this->package->id,
                'name' => $this->package->name,
                'type' => $this->package->type,
                'days' => $this->package->days,
                'price' => $this->package->price,
            ],

            'auction' => [
                'id' => $this->auction->id,
                'title' => $this->auction->title,
                'description' => $this->auction->description,
                'location' => $this->auction->location,
                'start_price' => $this->auction->start_price,
                'buy_now_price' => $this->auction->buy_now_price,
                'current_price' => $this->auction->current_price,
                'start_at' => $this->auction->start_at,
                'end_at' => $this->auction->end_at,
                'status' => $this->auction->status,
                'views' => $this->auction->views,
                'bids_count' => $this->auction->bids_count,
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
                'images' => $this->auction->getMedia('images')->map(fn($media) => $media->getUrl()),
                'videos' => $this->auction->getMedia('videos')->map(fn($media) => $media->getUrl()),
            ],
        ];
    }
}