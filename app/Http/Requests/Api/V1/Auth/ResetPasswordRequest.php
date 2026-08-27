<?php

namespace App\Http\Requests\Api\V1\Auth;

use App\Concerns\PasswordValidationRules;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
{
    use PasswordValidationRules;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['bail', 'nullable', 'required_without:phone', 'email', 'max:255'],
            'phone' => ['bail', 'nullable', 'required_without:email', 'string', 'max:25'],
            'code' => ['bail', 'required', 'string', 'size:6'],
            'password' => $this->passwordRules(),
        ];
    }
}
