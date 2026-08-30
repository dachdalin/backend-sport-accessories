<?php

namespace App\Http\Requests\Backend;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
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
            'email' => ['bail', 'required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['bail', 'required', 'string', Password::default(), 'confirmed'],
            'status' => ['bail', 'sometimes', 'boolean'],
            'roles' => ['bail', 'sometimes', 'array'],
            'roles.*' => ['bail', 'integer', Rule::exists('roles', 'id')],
        ];
    }
}
