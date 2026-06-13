<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            'username' => 'required|string|max:255|unique:users,username',

            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',

            'email' => 'required|string|email|max:255|unique:users,email',

            'phone_number' => 'nullable|string|max:20',

            'password' => 'required|string|min:8|confirmed',

            'user_type' => 'nullable|string|in:user,company',

            'passport' => 'required_if:user_type,company|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'commercial_register' => 'required_if:user_type,company|file|mimes:jpg,jpeg,png,pdf|max:2048',

            'notifications_enabled' => 'boolean',
            'email_enabled' => 'boolean',
            'status' => 'boolean',
            
        ];
    }
    
}
