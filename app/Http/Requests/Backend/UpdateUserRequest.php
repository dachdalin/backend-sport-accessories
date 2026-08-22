<?php

namespace App\Http\Requests\Backend;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
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
            'email' => ['bail', 'required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->route('user'))],
            'password' => ['bail', 'nullable', 'string', Password::default(), 'confirmed'],
            'roles' => ['bail', 'sometimes', 'array'],
            'roles.*' => ['bail', 'integer', Rule::exists('roles', 'id')],
        ];
    }
}
