<?php

namespace App\Actions\Categories;

use App\Models\Category;
use App\Services\CategorySlugService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class UpdateCategoryAction
{
    public function __construct(private readonly CategorySlugService $slugService) {}

    /**
     * @param  array{name: string, parent_id: ?int, position: int, home_status: bool}  $data
     */
    public function handle(Category $category, array $data, ?UploadedFile $icon): Category
    {
        $newPath = null;
        $oldPath = $category->icon;
        $oldDisk = $category->icon_storage_type;

        try {
            $category = DB::transaction(function () use ($category, $data, $icon, &$newPath) {
                if ($data['name'] !== $category->name) {
                    $data['slug'] = $this->slugService->generate($data['name'], $category->id);
                }

                if ($icon) {
                    $newPath = $icon->store('categories', 'public');
                    $data['icon'] = $newPath;
                }

                $category->update($data);

                return $category;
            });
        } catch (Throwable $e) {
            if ($newPath) {
                Storage::disk('public')->delete($newPath);
            }

            throw $e;
        }

        if ($newPath && $oldPath !== 'def.png') {
            Storage::disk($oldDisk)->delete($oldPath);
        }

        return $category;
    }
}
