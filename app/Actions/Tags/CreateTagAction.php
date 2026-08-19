<?php

namespace App\Actions\Tags;

use App\Models\Tag;
use App\Services\Tags\TagService;
use Illuminate\Support\Facades\DB;

class CreateTagAction
{
    public function __construct(private TagService $tags) {}

    /**
     * @param  array{tag: string}  $data
     */
    public function handle(array $data): Tag
    {
        $data['tag'] = $this->tags->normalize($data['tag']);

        return DB::transaction(fn () => Tag::create($data));
    }
}
