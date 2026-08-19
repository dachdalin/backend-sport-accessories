<?php

namespace App\Http\Requests\Backend;

use App\Enums\SearchFunctionVisibility;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class StoreSearchFunctionRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'key' => [
                'bail', 'required', 'string', 'max:150', 'regex:/^[\pL\pN\s\-]+$/u',
                Rule::unique('search_functions')->where('visible_for', $this->input('visible_for')),
            ],
            'url' => ['bail', 'required', 'string', 'max:250', 'regex:#^(https?://|/)#i'],
            'visible_for' => ['bail', 'required', new Enum(SearchFunctionVisibility::class)],
        ];
    }
}
