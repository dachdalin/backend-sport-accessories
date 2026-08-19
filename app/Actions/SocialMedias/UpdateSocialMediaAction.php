<?php

namespace App\Actions\SocialMedias;

use App\Models\SocialMedia;
use Illuminate\Support\Facades\DB;

class UpdateSocialMediaAction
{
    /**
     * @param  array{name: string, link: string, icon: ?string, status: bool}  $data
     */
    public function handle(SocialMedia $socialMedia, array $data): SocialMedia
    {
        DB::transaction(function () use ($socialMedia, $data) {
            $socialMedia->update($data);
        });

        return $socialMedia;
    }
}
