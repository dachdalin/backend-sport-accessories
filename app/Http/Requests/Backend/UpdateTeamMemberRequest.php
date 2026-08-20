<?php

namespace App\Http\Requests\Backend;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTeamMemberRequest extends FormRequest
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
            'role' => ['bail', 'required', 'string', 'max:100'],
            'bio' => ['bail', 'nullable', 'string', 'max:2000'],
            'photo' => ['bail', 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'photo_alt_text' => ['bail', 'nullable', 'string', 'max:255'],
            'sort_order' => ['bail', 'sometimes', 'integer', 'min:0'],
            'status' => ['bail', 'sometimes', 'boolean'],
        ];
    }
}
