<?php

namespace App\Actions\SoftCredentials;

use App\Models\SoftCredential;
use Illuminate\Support\Facades\DB;

class DeleteSoftCredentialAction
{
    public function handle(SoftCredential $credential): void
    {
        DB::transaction(function () use ($credential) {
            $credential->delete();
        });
    }
}
