<?php

namespace App\Actions\SoftCredentials;

use App\Models\SoftCredential;
use Illuminate\Support\Facades\DB;

class CreateSoftCredentialAction
{
    /**
     * @param  array{key: string, value: string}  $data
     */
    public function handle(array $data): SoftCredential
    {
        return DB::transaction(fn () => SoftCredential::create($data));
    }
}
