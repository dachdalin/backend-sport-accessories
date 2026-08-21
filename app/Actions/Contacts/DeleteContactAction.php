<?php

namespace App\Actions\Contacts;

use App\Models\Contact;
use Illuminate\Support\Facades\DB;

class DeleteContactAction
{
    public function handle(Contact $contact): void
    {
        DB::transaction(function () use ($contact) {
            $contact->delete();
        });
    }
}
