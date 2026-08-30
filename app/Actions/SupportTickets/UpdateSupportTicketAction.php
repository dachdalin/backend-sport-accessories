<?php

namespace App\Actions\SupportTickets;

use App\Models\SupportTicket;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class UpdateSupportTicketAction
{
    /**
     * @param  array{customer_id: int, subject: string, type: ?string, priority: string, description: string, reply: ?string, status: string}  $data
     */
    public function handle(SupportTicket $supportTicket, array $data, ?UploadedFile $attachment): SupportTicket
    {
        $newPath = null;
        $oldPath = $supportTicket->attachment;
        $oldDisk = $supportTicket->attachment_storage_type;

        try {
            $supportTicket = DB::transaction(function () use ($supportTicket, $data, $attachment, &$newPath) {
                if ($attachment) {
                    $newPath = $attachment->store('support-tickets', 'cloudinary');
                    $data['attachment'] = $newPath;
                    $data['attachment_storage_type'] = 'cloudinary';
                }

                $supportTicket->update($data);

                return $supportTicket;
            });
        } catch (Throwable $e) {
            if ($newPath) {
                Storage::disk('cloudinary')->delete($newPath);
            }

            throw $e;
        }

        if ($newPath && $oldPath) {
            Storage::disk($oldDisk)->delete($oldPath);
        }

        return $supportTicket;
    }
}
