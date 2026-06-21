<?php

namespace App\Http\Resources;

use App\Enums\PromotionStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuctionPromotionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        $promotion = ($this->status == PromotionStatus::PENDING->value)
            ? ($this->payable ?? $this->source)
            : ($this->payable ?? $this->source);

        $package = $promotion?->package;

        $showCallback = in_array($this->status, [
            PromotionStatus::PENDING->value,
            PromotionStatus::CANCELLED->value
        ]);

        return array_filter([
            'payment_id' => $this->id,
            'merchant_ref' => $this->merchant_ref,
            'amount' => $this->amount,
            'payment_status' => $this->status,
            'starts_at' => $this->activeSource?->starts_at,
            'expires_at' => $this->activeSource?->expires_at,
            'status' => $this->activeSource?->status,

            'package_details' => $package ? [
                'id' => $package->id,
                'name' => $package->name,
                'type' => $package->type,
                'days' => $package->days,
                'price' => $package->price,
                'is_active' => (bool) $package->is_active,
            ] : null,

            'callback_url' => $showCallback ? route('payments.callback', $this->merchant_ref) : null,
        ], fn($value) => !is_null($value));
    }

}