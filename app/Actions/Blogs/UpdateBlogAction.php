<?php

namespace App\Actions\Blogs;

use App\Models\Blog;
use App\Services\BlogSlugService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class UpdateBlogAction
{
    public function __construct(private readonly BlogSlugService $slugService) {}

    /**
     * @param  array{blog_category_id: ?int, title: string, writer: ?string, description: string, image_alt_text: ?string, is_published: bool, published_at: ?string}  $data
     */
    public function handle(Blog $blog, array $data, ?UploadedFile $image): Blog
    {
        $newPath = null;
        $oldPath = $blog->image;
        $oldDisk = $blog->image_storage_type;

        try {
            $blog = DB::transaction(function () use ($blog, $data, $image, &$newPath) {
                if ($data['title'] !== $blog->title) {
                    $data['slug'] = $this->slugService->generate($data['title'], $blog->id);
                }

                if ($image) {
                    $newPath = $image->store('blogs', 'public');
                    $data['image'] = $newPath;
                }

                $blog->update($data);

                return $blog;
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

        return $blog;
    }
}
