<?php

namespace App\Http\Requests\Backend;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLoyaltyTierRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['bail', 'required', 'string', 'max:50', Rule::unique('loyalty_tiers', 'name')->ignore($this->route('loyalty_tier'))],
            'points_required' => ['bail', 'required', 'integer', 'min:0'],
            'discount_percentage' => ['bail', 'required', 'integer', 'min:0', 'max:100'],
            'status' => ['bail', 'sometimes', 'boolean'],
        ];
    }
}
