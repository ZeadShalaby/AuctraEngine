<?php

namespace App\Http\Requests\Reports;

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
            'reportable_type' => 'required|string|in:post,comment,user,reel,ads,auction',

            'reportable_id' => [
                'required',
                'integer',
                Rule::exists($this->getActualTable(), 'id'),
                Rule::unique('reports', 'reportable_id')->where(function ($query) {
                    return $query->where('reportable_type', $this->input('reportable_type'))
                        ->where('user_id', auth()->id());
                }),
            ],

            'description' => 'required|string|max:1000',
        ];
    }
    // ? this function is used to get the actual table name based on the reportable_type input
    private function getActualTable(): string
    {
        $tables = [
            'post' => 'posts',
            'comment' => 'comments',
            'user' => 'users',
            'reel' => 'reels',
            'ads' => 'ads',
            'auction' => 'auctions',
        ];

        $type = $this->input('reportable_type');

        return $tables[$type] ?? 'users';
    }

    public function messages(): array
    {
        return [
            'reportable_id.unique' => __('messages.report_already_exists'),
        ];
    }
}