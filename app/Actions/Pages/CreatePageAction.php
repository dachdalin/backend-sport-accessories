<?php

namespace App\Actions\Pages;

use App\Models\Page;
use App\Services\PageSlugService;
use Illuminate\Support\Facades\DB;

class CreatePageAction
{
    public function __construct(private readonly PageSlugService $slugService) {}

    /**
     * @param  array{title: string, content: string, meta_title: ?string, meta_description: ?string, status: bool}  $data
     */
    public function handle(array $data): Page
    {
        return DB::transaction(function () use ($data) {
            $data['slug'] = $this->slugService->generate($data['title']);

            return Page::create($data);
        });
    }
}
