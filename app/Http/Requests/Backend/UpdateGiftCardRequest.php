<?php

namespace App\Http\Requests\Backend;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UpdateGiftCardRequest extends FormRequest
{
    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('code')) {
            $this->merge(['code' => Str::upper($this->input('code'))]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'code' => [
                'bail', 'required', 'string', 'max:30', 'regex:/^[A-Z0-9\-]+$/',
                Rule::unique('gift_cards', 'code')->ignore($this->route('gift_card')),
            ],
            'balance' => ['bail', 'required', 'numeric', 'min:0', 'max:'.$this->route('gift_card')->initial_balance],
            'expires_at' => ['bail', 'nullable', 'date'],
            'status' => ['bail', 'sometimes', 'boolean'],
        ];
    }
}
