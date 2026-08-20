<?php

namespace App\Actions\Pages;

use App\Models\Page;
use App\Services\PageSlugService;
use Illuminate\Support\Facades\DB;

class UpdatePageAction
{
    public function __construct(private readonly PageSlugService $slugService) {}

    /**
     * @param  array{title: string, content: string, meta_title: ?string, meta_description: ?string, status: bool}  $data
     */
    public function handle(Page $page, array $data): Page
    {
        return DB::transaction(function () use ($page, $data) {
            if ($data['title'] !== $page->title) {
                $data['slug'] = $this->slugService->generate($data['title'], $page->id);
            }

            $page->update($data);

            return $page;
        });
    }
}
