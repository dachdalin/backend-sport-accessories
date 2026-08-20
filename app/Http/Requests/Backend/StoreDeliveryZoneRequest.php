<?php

namespace App\Http\Requests\Backend;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreDeliveryZoneRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'zip_code' => ['bail', 'required', 'string', 'max:20', 'unique:delivery_zones,zip_code'],
            'city' => ['bail', 'nullable', 'string', 'max:100'],
            'delivery_charge' => ['bail', 'required', 'numeric', 'min:0'],
            'status' => ['bail', 'sometimes', 'boolean'],
        ];
    }
}
