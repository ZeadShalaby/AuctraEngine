<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuctionReelsResource extends JsonResource
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
            'winner_id' => $this->winner_id,
            'category' => $this->category->{'name_' . app()->getLocale()} ?? $this->category->name_en,
            'title' => $this->title,
            'location' => $this->location,
            'current_price' => $this->current_price,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'bids_count' => $this->bids_count,
            'status' => $this->status,
        ];
    }
}
