<?php

namespace App\Http\Requests\Backend;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['bail', 'required', 'string', 'max:100'],
            'icon' => ['bail', 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'parent_id' => ['bail', 'nullable', 'integer', 'exists:categories,id'],
            'position' => ['bail', 'sometimes', 'integer', 'min:0'],
            'home_status' => ['bail', 'sometimes', 'boolean'],
        ];
    }
}
