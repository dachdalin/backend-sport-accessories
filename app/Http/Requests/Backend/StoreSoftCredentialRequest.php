<?php

namespace App\Http\Requests\Backend;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreSoftCredentialRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'key' => ['bail', 'required', 'string', 'max:191', 'regex:/^[A-Z][A-Z0-9_]*$/', 'unique:soft_credentials,key'],
            'value' => ['bail', 'required', 'string', 'max:8192'],
        ];
    }
}
