<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\Reviews\CreateReviewAction;
use App\Enums\ReviewStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreReviewRequest;
use App\Http\Resources\Api\V1\ReviewResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ReviewController extends Controller
{
    /**
     * Display a paginated listing of the approved reviews for the given product.
     */
    public function index(Product $product): AnonymousResourceCollection
    {
        abort_unless($product->status, 404);

        return ReviewResource::collection(
            $product->reviews()
                ->where('status', ReviewStatus::Approved)
                ->latest()
                ->paginate(15)
                ->withQueryString(),
        );
    }

    /**
     * Store a newly submitted review for the given product, pending approval.
     */
    public function store(StoreReviewRequest $request, Product $product, CreateReviewAction $action): JsonResponse
    {
        abort_unless($product->status, 404);

        $customer = $request->user();

        $review = $action->handle([
            'product_id' => $product->id,
            'customer_name' => $customer->name,
            'customer_email' => $customer->email,
            'rating' => $request->validated('rating'),
            'comment' => $request->validated('comment'),
            'admin_reply' => null,
            'status' => ReviewStatus::Pending->value,
        ]);

        return (new ReviewResource($review))
            ->response()
            ->setStatusCode(JsonResponse::HTTP_CREATED);
    }
}
