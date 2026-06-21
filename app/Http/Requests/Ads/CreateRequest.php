<?php

namespace App\Http\Requests\Ads;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'feed_type' => ['required', 'string', Rule::in(['reels', 'posts', 'both'])],
            'link_url' => ['nullable', 'url', 'max:2048'],
            'payment_type' => ['required', 'string', Rule::in(['wallet', 'moamalat'])],
            'ad_price_id' => ['required', 'integer', 'exists:ad_prices,id'],
            'video' => ['required', 'file', 'mimetypes:video/mp4,video/quicktime,video/x-m4v,video/*', 'max:20480'],
            'image' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            'auction_id' => ['nullable', 'integer', 'exists:auctions,id'],
        ];
    }
}