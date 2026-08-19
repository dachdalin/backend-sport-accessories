<?php

namespace App\Actions\Categories;

use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DeleteCategoryAction
{
    public function handle(Category $category): void
    {
        $path = $category->icon;
        $disk = $category->icon_storage_type;

        DB::transaction(function () use ($category) {
            $category->delete();
        });

        if ($path !== 'def.png') {
            Storage::disk($disk)->delete($path);
        }
    }
}
