<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class ImageRequest extends FormRequest
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
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'passport' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'commercial_register' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }
}
