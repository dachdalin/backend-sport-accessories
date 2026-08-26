<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreShippingAddressRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'contact_person_name' => ['bail', 'required', 'string', 'max:100'],
            'phone' => ['bail', 'nullable', 'string', 'max:30'],
            'address_type' => ['bail', 'required', 'string', Rule::in(['home', 'office', 'other'])],
            'address' => ['bail', 'required', 'string', 'max:255'],
            'city' => ['bail', 'required', 'string', 'max:100'],
            'state' => ['bail', 'nullable', 'string', 'max:100'],
            'zip' => ['bail', 'nullable', 'string', 'max:20'],
            'country' => ['bail', 'required', 'string', 'max:100'],
            'is_default' => ['bail', 'sometimes', 'boolean'],
        ];
    }
}
