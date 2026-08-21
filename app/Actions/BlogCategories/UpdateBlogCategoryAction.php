<?php

namespace App\Actions\BlogCategories;

use App\Models\BlogCategory;
use App\Services\BlogCategorySlugService;
use Illuminate\Support\Facades\DB;

class UpdateBlogCategoryAction
{
    public function __construct(private readonly BlogCategorySlugService $slugService) {}

    /**
     * @param  array{name: string, status: bool}  $data
     */
    public function handle(BlogCategory $blogCategory, array $data): BlogCategory
    {
        return DB::transaction(function () use ($blogCategory, $data) {
            if ($data['name'] !== $blogCategory->name) {
                $data['slug'] = $this->slugService->generate($data['name'], $blogCategory->id);
            }

            $blogCategory->update($data);

            return $blogCategory;
        });
    }
}
