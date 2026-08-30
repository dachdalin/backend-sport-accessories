<?php

namespace App\Actions\Blogs;

use App\Models\Blog;
use App\Services\BlogSlugService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class CreateBlogAction
{
    public function __construct(private readonly BlogSlugService $slugService) {}

    /**
     * @param  array{blog_category_id: ?int, title: string, writer: ?string, description: string, image_alt_text: ?string, is_published: bool, published_at: ?string}  $data
     */
    public function handle(array $data, ?UploadedFile $image): Blog
    {
        $storedPath = null;

        try {
            return DB::transaction(function () use ($data, $image, &$storedPath) {
                $data['slug'] = $this->slugService->generate($data['title']);

                if ($image) {
                    $storedPath = $image->store('blogs', 'cloudinary');
                    $data['image'] = $storedPath;
                    $data['image_storage_type'] = 'cloudinary';
                }

                return Blog::create($data);
            });
        } catch (Throwable $e) {
            if ($storedPath) {
                Storage::disk('cloudinary')->delete($storedPath);
            }

            throw $e;
        }
    }
}
