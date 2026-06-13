<?php

namespace App\Http\Requests\Commments;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'commentable_type' => [
                'required',
                Rule::in(['post', 'reel']),
            ],

            'commentable_id' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) {

                    $map = [
                        'post' => \App\Models\Post::class,
                        'reel' => \App\Models\Reels::class,
                    ];

                    $type = $this->commentable_type;

                    if (!isset($map[$type])) {
                        return $fail('Invalid type.');
                    }

                    $model = $map[$type];

                    $exists = $model::query()->where('id', $value)->exists();

                    if (!$exists) {
                        $fail('The selected item does not exist.');
                    }
                }
            ],
        ];
    }
}