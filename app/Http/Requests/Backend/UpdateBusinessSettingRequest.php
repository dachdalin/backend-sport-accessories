<?php

namespace App\Http\Requests\Backend;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateBusinessSettingRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'site_name' => ['bail', 'required', 'string', 'max:191'],
            'logo' => ['bail', 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'contact_email' => ['bail', 'nullable', 'email', 'max:191'],
            'contact_phone' => ['bail', 'nullable', 'string', 'max:25'],
            'address' => ['bail', 'nullable', 'string', 'max:255'],
            'currency_symbol' => ['bail', 'required', 'string', 'max:5'],
            'minimum_order_amount' => ['bail', 'nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'free_delivery_over_amount' => ['bail', 'nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'tax_included_in_price' => ['bail', 'sometimes', 'boolean'],
            'maintenance_mode' => ['bail', 'sometimes', 'boolean'],
            'copyright_text' => ['bail', 'nullable', 'string', 'max:255'],
            'meta_title' => ['bail', 'nullable', 'string', 'max:191'],
            'meta_description' => ['bail', 'nullable', 'string', 'max:191'],
        ];
    }
}
