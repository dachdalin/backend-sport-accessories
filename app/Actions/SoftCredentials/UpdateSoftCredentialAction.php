<?php

namespace App\Actions\SoftCredentials;

use App\Models\SoftCredential;
use Illuminate\Support\Facades\DB;

class UpdateSoftCredentialAction
{
    /**
     * @param  array{key: string, value: ?string}  $data
     */
    public function handle(SoftCredential $credential, array $data): SoftCredential
    {
        // A blank value means "keep the current secret" — never overwrite it
        // with an empty string just because the field was left untouched.
        if (blank($data['value'] ?? null)) {
            unset($data['value']);
        }

        DB::transaction(function () use ($credential, $data) {
            $credential->update($data);
        });

        return $credential;
    }
}
