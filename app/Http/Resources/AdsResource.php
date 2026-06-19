<?php

namespace App\Http\Resources;

use App\Http\Resources\AuctionReelsResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'title' => $this->title,
            'description' => $this->description,

            'type' => $this->feed_type,
            'status' => $this->status,
            'starts_at' => optional($this->starts_at)->format('Y-m-d'),
            'expires_at' => optional($this->expires_at)->format('Y-m-d'),

            'max_impressions' => $this->max_impressions,
            'current_impressions' => $this->current_impressions,

            'link_url' => $this->link_url,


            'adable' => $this->whenLoaded('adable', function () {
                return [
                    'id' => $this->adable->id,
                    'type' => class_basename(get_class($this->adable)),
                ];
            }),

            'user' => new ReelUserResource($this->whenLoaded('user')),

            'auction' => new AuctionReelsResource($this->whenLoaded('auction')),

            'media' => $this->whenLoaded('media', function () {
                return $this->media->map(function ($media) {
                    return [
                        'id' => $media->id,
                        'mime_type' => $media->mime_type,
                        'url' => $media->original_url,
                    ];
                });
            }),
        ];
    }
}