<?php

namespace App\Actions\Tags;

use App\Models\Tag;
use Illuminate\Support\Facades\DB;

class DeleteTagAction
{
    public function handle(Tag $tag): void
    {
        DB::transaction(function () use ($tag) {
            $tag->delete();
        });
    }
}
