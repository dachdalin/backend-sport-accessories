<?php

namespace App\Http\Requests\Backend;

use App\Enums\SupportTicketPriority;
use App\Enums\SupportTicketStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSupportTicketRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'customer_id' => ['bail', 'required', 'integer', Rule::exists('customers', 'id')],
            'subject' => ['bail', 'required', 'string', 'max:150'],
            'type' => ['bail', 'nullable', 'string', 'max:50'],
            'priority' => ['bail', 'required', 'string', Rule::enum(SupportTicketPriority::class)],
            'description' => ['bail', 'required', 'string', 'max:5000'],
            'attachment' => ['bail', 'nullable', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:5120'],
            'reply' => ['bail', 'nullable', 'string', 'max:5000'],
            'status' => ['bail', 'required', 'string', Rule::enum(SupportTicketStatus::class)],
        ];
    }
}
