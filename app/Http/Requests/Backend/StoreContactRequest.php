<?php

namespace App\Http\Requests\Backend;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreContactRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['bail', 'required', 'string', 'max:191'],
            'email' => ['bail', 'required', 'email', 'max:191'],
            'phone' => ['bail', 'nullable', 'string', 'max:25'],
            'subject' => ['bail', 'required', 'string', 'max:191'],
            'message' => ['bail', 'required', 'string', 'max:5000'],
            'reply' => ['bail', 'nullable', 'string', 'max:5000'],
            'status' => ['bail', 'sometimes', 'boolean'],
        ];
    }
}
