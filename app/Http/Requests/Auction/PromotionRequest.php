<?php

namespace App\Http\Requests\Auction;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class PromotionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "package_id" => "required|exists:promotion_packages,id",
            'auction_id' => [
                'required',
                Rule::exists('auctions', 'id')->where(function ($query) {
                    $query->where('user_id', auth()->id());
                }),
            ],
            "payment_type" => "required|string|in:wallet,moamalat",
        ];
    }
}