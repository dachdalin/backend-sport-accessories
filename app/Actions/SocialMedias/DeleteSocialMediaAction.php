<?php

namespace App\Actions\SocialMedias;

use App\Models\SocialMedia;
use Illuminate\Support\Facades\DB;

class DeleteSocialMediaAction
{
    public function handle(SocialMedia $socialMedia): void
    {
        DB::transaction(function () use ($socialMedia) {
            $socialMedia->delete();
        });
    }
}
