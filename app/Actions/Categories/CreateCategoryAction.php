<?php

namespace App\Actions\Categories;

use App\Models\Category;
use App\Services\CategorySlugService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class CreateCategoryAction
{
    public function __construct(private readonly CategorySlugService $slugService) {}

    /**
     * @param  array{name: string, parent_id: ?int, position: int, home_status: bool}  $data
     */
    public function handle(array $data, ?UploadedFile $icon): Category
    {
        $storedPath = null;

        try {
            return DB::transaction(function () use ($data, $icon, &$storedPath) {
                $data['slug'] = $this->slugService->generate($data['name']);

                if ($icon) {
                    $storedPath = $icon->store('categories', 'cloudinary');
                    $data['icon'] = $storedPath;
                    $data['icon_storage_type'] = 'cloudinary';
                }

                return Category::create($data);
            });
        } catch (Throwable $e) {
            if ($storedPath) {
                Storage::disk('cloudinary')->delete($storedPath);
            }

            throw $e;
        }
    }
}
