<?php

namespace App\Actions\Tags;

use App\Models\Tag;
use App\Services\Tags\TagService;
use Illuminate\Support\Facades\DB;

class UpdateTagAction
{
    public function __construct(private TagService $tags) {}

    /**
     * @param  array{tag: string}  $data
     */
    public function handle(Tag $tag, array $data): Tag
    {
        $data['tag'] = $this->tags->normalize($data['tag']);

        DB::transaction(function () use ($tag, $data) {
            $tag->update($data);
        });

        return $tag;
    }
}
