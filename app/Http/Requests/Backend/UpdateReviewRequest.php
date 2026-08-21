<?php

namespace App\Http\Requests\Backend;

use App\Enums\ReviewStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateReviewRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'product_id' => ['bail', 'required', 'integer', Rule::exists('products', 'id')],
            'customer_name' => ['bail', 'required', 'string', 'max:100'],
            'customer_email' => ['bail', 'nullable', 'email', 'max:191'],
            'rating' => ['bail', 'required', 'integer', 'min:1', 'max:5'],
            'comment' => ['bail', 'required', 'string', 'max:5000'],
            'admin_reply' => ['bail', 'nullable', 'string', 'max:5000'],
            'status' => ['bail', 'required', 'string', Rule::enum(ReviewStatus::class)],
        ];
    }
}
