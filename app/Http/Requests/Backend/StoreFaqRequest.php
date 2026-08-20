<?php

namespace App\Http\Requests\Backend;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreFaqRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'question' => ['bail', 'required', 'string', 'max:500'],
            'answer' => ['bail', 'required', 'string', 'max:5000'],
            'sort_order' => ['bail', 'sometimes', 'integer', 'min:0'],
            'status' => ['bail', 'sometimes', 'boolean'],
        ];
    }
}
