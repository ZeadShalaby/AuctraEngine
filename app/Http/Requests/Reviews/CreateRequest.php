<?php

namespace App\Http\Requests\Reviews;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Auction;

class CreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'seller_id' => 'required|exists:users,id',
            'auction_id' => 'required|exists:auctions,id',
            'rating' => 'required|integer|between:1,5',
            'content' => 'required|string|max:255',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {

            $auction = Auction::find($this->auction_id);

            if (!$auction) {
                return;
            }

            if ((int) $this->seller_id !== (int) $auction->user_id) {
                $validator->errors()->add(
                   __('messages.wrong_seller'),
                    __('messages.wrong_not_seller')
                );
            }

            if (auth()->id() === $auction->user_id) {
                $validator->errors()->add(
                    __('messages.wrong_seller_auction'),
                    __('messages.wrong_seller_not_auction')
                );
            }
        });
    }
}