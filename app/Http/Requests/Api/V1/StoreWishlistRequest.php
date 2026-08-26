<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreWishlistRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'product_id' => [
                'bail',
                'required',
                'integer',
                Rule::exists('products', 'id'),
                Rule::unique('wishlists')->where(fn ($query) => $query->where('customer_email', $this->user()->email)),
            ],
        ];
    }
}
