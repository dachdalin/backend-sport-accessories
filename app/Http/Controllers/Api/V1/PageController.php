<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\PageResource;
use App\Models\Page;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PageController extends Controller
{
    /**
     * Display a paginated listing of the active pages.
     */
    public function index(): AnonymousResourceCollection
    {
        return PageResource::collection(
            Page::query()->where('status', true)->latest()->paginate(15)->withQueryString(),
        );
    }

    /**
     * Display the specified active page.
     */
    public function show(Page $page): PageResource
    {
        abort_unless($page->status, 404);

        return new PageResource($page);
    }
}
