<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
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
            'rating' => $this->rating,
            'comments' => $this->whenLoaded('comments'),

            'reviewer' => [
                'id' => $this->reviewer->id,
                'username' => $this->reviewer->username,
                'full_name' => $this->reviewer->full_name,
                'phone_number' => $this->reviewer->phone_number,
                'profile_image' => $this->reviewer->getProfileImage(),
                'email' => $this->reviewer->email,
            ],

            'seller' => $this->when(
                $this->relationLoaded('seller') && $this->seller,
                function () {
                    return [
                        'id' => $this->seller->id,
                        'username' => $this->seller->username,
                        'full_name' => $this->seller->full_name,
                        'phone_number' => $this->seller->phone_number,
                        'profile_image' => $this->seller->getProfileImage(),

                        'profile' => $this->when(
                            $this->seller->userProfile,
                            [
                                'company_name' => $this->seller->userProfile->company_name,
                                'city' => $this->seller->userProfile->city,
                                'country' => $this->seller->userProfile->country,
                            ]
                        ),
                    ];
                }
            ),

            'auction' => AuctionPreviewResource::make($this->whenLoaded('auction')),

            'created_at' => $this->created_at?->diffForHumans(),
        ];
    }
}