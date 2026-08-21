<?php

namespace App\Actions\BlogCategories;

use App\Models\BlogCategory;
use App\Services\BlogCategorySlugService;
use Illuminate\Support\Facades\DB;

class CreateBlogCategoryAction
{
    public function __construct(private readonly BlogCategorySlugService $slugService) {}

    /**
     * @param  array{name: string, status: bool}  $data
     */
    public function handle(array $data): BlogCategory
    {
        return DB::transaction(function () use ($data) {
            $data['slug'] = $this->slugService->generate($data['name']);

            return BlogCategory::create($data);
        });
    }
}
