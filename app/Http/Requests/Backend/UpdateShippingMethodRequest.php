<?php

namespace App\Http\Requests\Backend;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateShippingMethodRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['bail', 'required', 'string', 'max:100'],
            'cost' => ['bail', 'required', 'numeric', 'min:0'],
            'duration' => ['bail', 'nullable', 'string', 'max:20'],
            'status' => ['bail', 'sometimes', 'boolean'],
        ];
    }
}
