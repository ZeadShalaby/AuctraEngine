<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "user_id" => $this->user->id,
            "name" => $this->user->full_name,
            "merchant_ref" => $this->merchant_ref,
            "amount" => $this->amount,
            "status" => $this->status,
            "payment_gateway" => $this->payment_gateway,
            "type" => $this->type,
        ];
    }
}
