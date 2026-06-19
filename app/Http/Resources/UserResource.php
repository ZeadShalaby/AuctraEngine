<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'username' => $this->name,
            'full_name' => $this->full_name,
            'status' => $this->status,
            'email' => $this->email,
            'name' => $this->full_name,
            'phonenumber' => $this->phone_number,
            'profile' => $this->userProfile,
            'media' => $this->getFirstMediaUrl('avatar') ?: asset('storage/images/default.png'),
            'details' => [
                'rating' => round($this->reviews_avg_rating ?? 0, 1),
                'reviews' => $this->reviews_count,
                'sold' => $this->sold_items_count,
            ],
        ];
    }
}
