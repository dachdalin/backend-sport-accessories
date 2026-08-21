<?php

namespace App\Actions\SupportTickets;

use App\Models\SupportTicket;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class CreateSupportTicketAction
{
    /**
     * @param  array{customer_id: int, subject: string, type: ?string, priority: string, description: string, reply: ?string, status: string}  $data
     */
    public function handle(array $data, ?UploadedFile $attachment): SupportTicket
    {
        $storedPath = null;

        try {
            return DB::transaction(function () use ($data, $attachment, &$storedPath) {
                if ($attachment) {
                    $storedPath = $attachment->store('support-tickets', 'public');
                    $data['attachment'] = $storedPath;
                }

                return SupportTicket::create($data);
            });
        } catch (Throwable $e) {
            if ($storedPath) {
                Storage::disk('public')->delete($storedPath);
            }

            throw $e;
        }
    }
}
