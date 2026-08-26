<?php

namespace App\Http\Resources\Api\V1;

use App\Models\SupportTicket;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

/** @mixin SupportTicket */
class SupportTicketResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'subject' => $this->subject,
            'type' => $this->type,
            'priority' => $this->priority,
            'description' => $this->description,
            'attachment_url' => $this->attachment ? Storage::disk($this->attachment_storage_type)->url($this->attachment) : null,
            'reply' => $this->reply,
            'status' => $this->status,
            'created_at' => $this->created_at,
        ];
    }
}
