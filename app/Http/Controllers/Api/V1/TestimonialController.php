<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\TestimonialResource;
use App\Models\Testimonial;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TestimonialController extends Controller
{
    /**
     * Display a paginated listing of the active testimonials.
     */
    public function index(): AnonymousResourceCollection
    {
        return TestimonialResource::collection(
            Testimonial::query()->where('status', true)->latest()->paginate(15)->withQueryString(),
        );
    }

    /**
     * Display the specified active testimonial.
     */
    public function show(Testimonial $testimonial): TestimonialResource
    {
        abort_unless($testimonial->status, 404);

        return new TestimonialResource($testimonial);
    }
}
