<?php

namespace App\Actions\BlogCategories;

use App\Models\BlogCategory;
use Illuminate\Support\Facades\DB;

class DeleteBlogCategoryAction
{
    public function handle(BlogCategory $blogCategory): void
    {
        DB::transaction(function () use ($blogCategory) {
            $blogCategory->delete();
        });
    }
}
