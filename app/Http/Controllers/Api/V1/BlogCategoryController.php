<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\BlogCategoryResource;
use App\Models\BlogCategory;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BlogCategoryController extends Controller
{
    /**
     * Display a paginated listing of the active blog categories.
     */
    public function index(): AnonymousResourceCollection
    {
        return BlogCategoryResource::collection(
            BlogCategory::query()->where('status', true)->latest()->paginate(15)->withQueryString(),
        );
    }

    /**
     * Display the specified active blog category.
     */
    public function show(BlogCategory $blogCategory): BlogCategoryResource
    {
        abort_unless($blogCategory->status, 404);

        return new BlogCategoryResource($blogCategory);
    }
}
