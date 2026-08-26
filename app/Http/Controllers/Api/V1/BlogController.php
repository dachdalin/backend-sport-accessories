<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\BlogResource;
use App\Models\Blog;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BlogController extends Controller
{
    /**
     * Display a paginated listing of the published blogs.
     */
    public function index(): AnonymousResourceCollection
    {
        return BlogResource::collection(
            Blog::query()
                ->where('is_published', true)
                ->where('published_at', '<=', now())
                ->with('category')
                ->latest('published_at')
                ->paginate(15)
                ->withQueryString(),
        );
    }

    /**
     * Display the specified published blog.
     */
    public function show(Blog $blog): BlogResource
    {
        abort_unless($blog->is_published && $blog->published_at?->isPast(), 404);

        return new BlogResource($blog->load('category'));
    }
}
