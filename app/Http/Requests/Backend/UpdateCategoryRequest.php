<?php

namespace App\Http\Requests\Backend;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateCategoryRequest extends FormRequest
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

    /**
     * @return array<int, callable>
     */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                if ((int) $this->input('parent_id') === $this->route('category')->id) {
                    $validator->errors()->add('parent_id', __('A category cannot be its own parent.'));
                }
            },
        ];
    }
}
