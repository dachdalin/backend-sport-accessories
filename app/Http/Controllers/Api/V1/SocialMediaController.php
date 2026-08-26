<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\SocialMediaResource;
use App\Models\SocialMedia;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SocialMediaController extends Controller
{
    /**
     * Display a paginated listing of the active social media links.
     */
    public function index(): AnonymousResourceCollection
    {
        return SocialMediaResource::collection(
            SocialMedia::query()->where('status', true)->latest()->paginate(15)->withQueryString(),
        );
    }

    /**
     * Display the specified active social media link.
     */
    public function show(SocialMedia $socialMedia): SocialMediaResource
    {
        abort_unless($socialMedia->status, 404);

        return new SocialMediaResource($socialMedia);
    }
}
