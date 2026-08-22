<?php

namespace App\Http\Requests\Backend;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRoleRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'bail',
                'required',
                'string',
                'max:125',
                'regex:/^[\pL\pN\s\-]+$/u',
                Rule::unique('roles', 'name')->where('guard_name', 'web')->ignore($this->route('role')),
            ],
            'permissions' => ['bail', 'sometimes', 'array'],
            'permissions.*' => ['bail', 'integer', Rule::exists('permissions', 'id')],
        ];
    }
}
