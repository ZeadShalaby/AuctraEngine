<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompleteAuctionPaymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'payment' => [
                'status' => 'paid',
                'amount' => (float) $this->amount,
                'paid_at' => now()->toDateTimeString(),
            ],

            'auction' => [
                'id' => $this->auction->id,
                'title' => $this->auction->title,
                'status' => $this->auction->status,
                'final_price' => (float) $this->amount,
            ],

            'winner' => [
                'id' => $this->auction->winner->id,
                'name' => $this->auction->winner->full_name,
                'username' => $this->auction->winner->username,
            ],
        ];
    }
}