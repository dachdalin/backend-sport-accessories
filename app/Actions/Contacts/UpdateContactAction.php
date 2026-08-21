<?php

namespace App\Actions\Contacts;

use App\Models\Contact;
use Illuminate\Support\Facades\DB;

class UpdateContactAction
{
    /**
     * @param  array{name: string, email: string, phone: ?string, subject: string, message: string, reply: ?string, status: bool}  $data
     */
    public function handle(Contact $contact, array $data): Contact
    {
        return DB::transaction(function () use ($contact, $data) {
            $contact->update($data);

            return $contact;
        });
    }
}
