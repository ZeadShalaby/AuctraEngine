<?php

namespace App\Http\Resources\Terms;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BuyTermResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'amount' => $this->when($this->amount > 0, (float) $this->amount),
            'created_at' => $this->created_at,
            'auction' => [
                'id' => $this->auction?->id ?? $this->id,
                'title' => $this->auction?->title ?? $this->title,
                'status' => $this->auction?->status ?? $this->status,
                'terms_price' => $this->auction?->terms_price ?? $this->terms_price,
                'buy_now_price' => $this->auction?->buy_now_price ?? $this->buy_now_price,
                'start_price' => $this->auction?->start_price ?? $this->start_price,
                'start_at' => $this->auction?->start_at ?? $this->start_at,
                'end_at' => $this->auction?->end_at ?? $this->end_at,
                'views' => $this->auction?->views ?? $this->views,
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

                'images' => $this->when(
                    $request->routeIs('auctions.show'),
                    fn() => $this->getMedia('images')->map(fn($media) => $media->getUrl())
                ),

                'videos' => $this->when(
                    $request->routeIs('auctions.show'),
                    fn() => $this->getMedia('videos')->map(fn($media) => $media->getUrl())
                ),
            ],

        ];
    }
}