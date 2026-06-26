<?php

namespace App\Http\Resources;

use App\Enums\AdsStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdPaymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        $ad = $this->payable ?? $this->source;
        $showCallback = !in_array($ad->status, [
            AdsStatus::ACTIVE->value,
            AdsStatus::REVIEW->value
        ]);
        return array_filter([
            'ad_id' => $this->payable_id ?? $this->source->id,

            'ad_details' => $ad ? array_filter([
                'title' => $ad->title,
                'description' => $ad->description,
                'status' => $ad->status,
                'starts_at' => $ad->starts_at?->format('Y-m-d'),
                'expires_at' => $ad->expires_at?->format('Y-m-d'),
                'link' => $ad->link_url,
                'feed_type' => $ad->feed_type,
                'image' => $ad->getMedia('image')
                    ->map(fn($media) => $media->getUrl())
                    ->values(),

                'video' => $ad->getMedia('video')
                    ->map(fn($media) => $media->getUrl())
                    ->values(),
            ], fn($value) => !is_null($value)) : null,

            'payment_status' => $this->status,
            'merchant_ref' => $this->merchant_ref,
            'amount' => $this->amount,

            'callback_url' => $showCallback ? route('payments.callback', $this->merchant_ref) : null,

        ], fn($value) => !is_null($value));
    }
}
