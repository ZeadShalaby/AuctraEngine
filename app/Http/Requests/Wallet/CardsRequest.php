<?php
namespace App\Http\Requests\Wallet;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule; // لا تنسى استيراد هذه المكتبة

class CardsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'payment_type' => 'required|string|in:moamalat,card',
            'cardnumber' => [
                'sometimes',
                'required',
                'string',
                Rule::exists('recharge_cards', 'card_number')->where(function ($query) {
                    $query->where('used', 0);
                }),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'cardnumber.exists' => __('messages.card_already_used_or_invalid'),
        ];
    }
}