<?php

namespace App\Http\Requests\Api\V1\Auth;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class TelegramLoginRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id' => ['bail', 'required', 'integer'],
            'first_name' => ['bail', 'required', 'string', 'max:255'],
            'last_name' => ['bail', 'nullable', 'string', 'max:255'],
            'username' => ['bail', 'nullable', 'string', 'max:255'],
            'photo_url' => ['bail', 'nullable', 'url', 'max:2048'],
            'auth_date' => ['bail', 'required', 'integer'],
            'hash' => ['bail', 'required', 'string'],
        ];
    }
}
