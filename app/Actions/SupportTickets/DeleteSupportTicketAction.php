<?php

namespace App\Actions\SupportTickets;

use App\Models\SupportTicket;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DeleteSupportTicketAction
{
    public function handle(SupportTicket $supportTicket): void
    {
        $path = $supportTicket->attachment;
        $disk = $supportTicket->attachment_storage_type;

        DB::transaction(function () use ($supportTicket) {
            $supportTicket->delete();
        });

        if ($path) {
            Storage::disk($disk)->delete($path);
        }
    }
}
