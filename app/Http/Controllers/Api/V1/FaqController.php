<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\FaqResource;
use App\Models\Faq;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class FaqController extends Controller
{
    /**
     * Display a paginated listing of the active FAQs.
     */
    public function index(): AnonymousResourceCollection
    {
        return FaqResource::collection(
            Faq::query()->where('status', true)->orderBy('sort_order')->paginate(15)->withQueryString(),
        );
    }

    /**
     * Display the specified active FAQ.
     */
    public function show(Faq $faq): FaqResource
    {
        abort_unless($faq->status, 404);

        return new FaqResource($faq);
    }
}
