<?php

namespace App\Http\Requests\Backend;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreSocialMediaRequest extends FormRequest
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
            'link' => ['bail', 'required', 'url', 'max:100'],
            'icon' => ['bail', 'nullable', 'string', 'max:100'],
            'status' => ['bail', 'sometimes', 'boolean'],
        ];
    }
}
