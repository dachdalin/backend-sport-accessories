<?php

namespace App\Actions\SocialMedias;

use App\Models\SocialMedia;
use Illuminate\Support\Facades\DB;

class CreateSocialMediaAction
{
    /**
     * @param  array{name: string, link: string, icon: ?string, status: bool}  $data
     */
    public function handle(array $data): SocialMedia
    {
        return DB::transaction(fn () => SocialMedia::create($data));
    }
}
