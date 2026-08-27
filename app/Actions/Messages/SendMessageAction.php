<?php

namespace App\Actions\Messages;

use App\Models\Message;
use Illuminate\Support\Facades\DB;

class SendMessageAction
{
    /**
     * @param  array{receiver_id: int, body: string}  $data
     */
    public function handle(array $data, int $senderId): Message
    {
        return DB::transaction(function () use ($data, $senderId) {
            return Message::create([
                ...$data,
                'sender_id' => $senderId,
            ]);
        });
    }
}
