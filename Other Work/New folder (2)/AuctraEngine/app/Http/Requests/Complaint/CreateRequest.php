<?php

namespace App\Http\Requests\Complaint;

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
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'description' => 'required|string|max:2000',
            'complaintable_type' => 'required|string|in:user,auction',
            'complaintable_id' => [
                'required',
                'integer',
                Rule::exists($this->getActualTable(), 'id'),
                
                Rule::unique('complaints', 'complaintable_id')->where(function ($query) {
                    return $query->where('complaintable_type', $this->input('complaintable_type'))
                                 ->where('email', $this->input('email')); // التحقق بالـ email بما أنه حقل مطلوب في الشكوى
                }),
            ],
        ];
    }

    // ? complaintable_type is a logical type that determines which table to check for the complaintable_id
    private function getActualTable(): string
    {
        $tables = [
            'user'    => 'users',
            'auction' => 'auctions',
        ];

        $type = $this->input('complaintable_type');

        return $tables[$type] ?? 'users'; 
    }

    public function messages(): array
    {
        return [
            'complaintable_id.unique' => __('messages.complaint_already_exists'),
        ];
    }
}