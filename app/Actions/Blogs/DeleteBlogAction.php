<?php

namespace App\Actions\Blogs;

use App\Models\Blog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DeleteBlogAction
{
    public function handle(Blog $blog): void
    {
        $path = $blog->image;
        $disk = $blog->image_storage_type;

        DB::transaction(function () use ($blog) {
            $blog->delete();
        });

        if ($path !== 'def.png') {
            Storage::disk($disk)->delete($path);
        }
    }
}
