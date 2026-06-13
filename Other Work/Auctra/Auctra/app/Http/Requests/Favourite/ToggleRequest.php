<?php

namespace App\Http\Requests\Favourite;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Relations\Relation;

class ToggleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $model = Relation::getMorphedModel($this->favoriteable_type);

        return [
            'favoriteable_type' => [
                'required',
                'string',
                Rule::in([ 'reel']), // ? post , ads
            ],

            'favoriteable_id' => [
                'required',
                'integer',
                $model
                ? Rule::exists((new $model)->getTable(), 'id')
                : function ($attribute, $value, $fail) {
                    $fail('Invalid favoriteable type.');
                },
            ],
        ];
    }
}