<?php

namespace App\Http\Requests\Backend;

use App\Enums\OfferActiveTime;
use App\Enums\TaxType;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreStockClearanceSetupRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'discount_type' => ['bail', 'required', 'string', Rule::enum(TaxType::class)],
            'discount_amount' => ['bail', 'required', 'numeric', 'min:0', 'max:99999999.99'],
            'offer_active_time' => ['bail', 'required', 'string', Rule::enum(OfferActiveTime::class)],
            'offer_active_range_start' => ['bail', 'required_if:offer_active_time,specific_time', 'nullable', 'date_format:H:i'],
            'offer_active_range_end' => ['bail', 'required_if:offer_active_time,specific_time', 'nullable', 'date_format:H:i', 'after:offer_active_range_start'],
            'show_in_homepage' => ['bail', 'sometimes', 'boolean'],
            'show_in_homepage_once' => ['bail', 'sometimes', 'boolean'],
            'show_in_shop' => ['bail', 'sometimes', 'boolean'],
            'is_active' => ['bail', 'sometimes', 'boolean'],
            'duration_start_date' => ['bail', 'required', 'date'],
            'duration_end_date' => ['bail', 'required', 'date', 'after_or_equal:duration_start_date'],
            'items' => ['bail', 'required', 'array', 'min:1'],
            'items.*.product_id' => ['bail', 'required', 'integer', Rule::exists('products', 'id')],
            'items.*.discount_type' => ['bail', 'required', 'string', Rule::enum(TaxType::class)],
            'items.*.discount_amount' => ['bail', 'required', 'numeric', 'min:0', 'max:99999999.99'],
            'items.*.is_active' => ['bail', 'sometimes', 'boolean'],
        ];
    }
}
