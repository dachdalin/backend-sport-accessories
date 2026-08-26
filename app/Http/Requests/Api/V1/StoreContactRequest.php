<?php

namespace App\Http\Requests\Api\V1;

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
        ];
    }
}
