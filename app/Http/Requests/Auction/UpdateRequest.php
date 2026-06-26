<?php

namespace App\Http\Requests\Auction;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'category_id' => [
                'required',
                'exists:categories,id',
            ],

            'sub_category_id' => [
                'nullable',
                'exists:sub_categories,id',
            ],

            'title' => [
                'required',
                'string',
                'min:5',
                'max:255',
            ],

            'description' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'location' => [
                'nullable',
                'string',
                'max:255',
            ],

            'start_price' => [
                'required',
                'numeric',
                'min:1',
            ],

            'min_bid_increment' => [
                'required',
                'numeric',
                'min:1',
            ],

            'buy_now_price' => [
                'nullable',
                'numeric',
                'gt:start_price',
            ],

            'start_at' => [
                'required',
                'date',
                'after_or_equal:now',
            ],

            'end_at' => [
                'required',
                'date',
                'after:start_at',
            ],

            'condition' => [
                'required',
                Rule::in(['new', 'used']),
            ],

            'images' => [
                'required',
                'array',
                'min:1',
            ],

            'images.*' => [
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],

            'videos' => [
                'nullable',
                'array',
            ],

            'videos.*' => [
                'mimes:mp4,mov,avi,mkv',
                'max:51200',
            ],

        ];
    }

    public function messages(): array
    {
        return [
            'buy_now_price.gt' => __('validation.buy_now_price'),
            'end_at.after' => __('validation.end_at'),
        ];
    }
}
