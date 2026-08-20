<?php

namespace App\Http\Requests\Backend;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePageRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['bail', 'required', 'string', 'max:150'],
            'content' => ['bail', 'required', 'string', 'max:20000'],
            'meta_title' => ['bail', 'nullable', 'string', 'max:150'],
            'meta_description' => ['bail', 'nullable', 'string', 'max:255'],
            'status' => ['bail', 'sometimes', 'boolean'],
        ];
    }
}
