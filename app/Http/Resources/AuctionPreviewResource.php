<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuctionPreviewResource extends JsonResource
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
            'title' => $this->title,
            'status' => $this->status,
            'current_price' => $this->current_price,
            'end_at' => $this->end_at?->diffForHumans(),

            'images' => $this->getMedia('images')->map(fn($img) => [
                'id' => $img->id,
                'url' => $img->getUrl(),
            ]),

            'videos' => $this->getMedia('videos')->map(fn($vid) => [
                'id' => $vid->id,
                'url' => $vid->getUrl(),
            ]),
        ];
    }
}
