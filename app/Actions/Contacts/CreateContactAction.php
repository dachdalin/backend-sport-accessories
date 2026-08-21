<?php

namespace App\Actions\Contacts;

use App\Models\Contact;
use Illuminate\Support\Facades\DB;

class CreateContactAction
{
    /**
     * @param  array{name: string, email: string, phone: ?string, subject: string, message: string, reply: ?string, status: bool}  $data
     */
    public function handle(array $data): Contact
    {
        return DB::transaction(fn () => Contact::create($data));
    }
}
