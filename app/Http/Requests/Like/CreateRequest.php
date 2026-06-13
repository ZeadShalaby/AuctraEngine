<?php

namespace App\Http\Requests\Like;

use Illuminate\Database\Eloquent\Relations\Relation;
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
            'likeable_id' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) {

                    $type = $this->likeable_type;

                    if (!$type) {
                        return $fail('likeable_type is required first.');
                    }

                    $modelClass = Relation::getMorphedModel($type);

                    if (!$modelClass) {
                        return $fail('Invalid likeable_type.');
                    }

                    $exists = $modelClass::where('id', $value)->exists();

                    if (!$exists) {
                        return $fail("This {$type} does not exist.");
                    }
                }
            ],

            'likeable_type' => [
                'required',
                'string',
                Rule::in(['post', 'reel', 'ads']),
            ],
        ];
    }
}
