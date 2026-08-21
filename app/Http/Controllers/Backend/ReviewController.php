<?php

namespace App\Http\Controllers\Backend;

use App\Actions\Reviews\CreateReviewAction;
use App\Actions\Reviews\DeleteReviewAction;
use App\Actions\Reviews\UpdateReviewAction;
use App\Enums\ReviewStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\StoreReviewRequest;
use App\Http\Requests\Backend\UpdateReviewRequest;
use App\Models\Product;
use App\Models\Review;
use App\Services\ReviewService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class ReviewController extends Controller
{
    public function __construct(private readonly ReviewService $reviewService) {}

    /**
     * Display a listing of the reviews.
     */
    public function index(): Response
    {
        return Inertia::render('reviews/Index', [
            'reviews' => $this->reviewService->list(),
        ]);
    }

    /**
     * Show the form for creating a new review.
     */
    public function create(): Response
    {
        return Inertia::render('reviews/Create', [
            'products' => $this->productOptions(),
            'statuses' => $this->statusOptions(),
        ]);
    }

    /**
     * Store a newly created review.
     */
    public function store(StoreReviewRequest $request, CreateReviewAction $action): RedirectResponse
    {
        try {
            $action->handle($request->validated());
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not create the review. Please try again.')]);

            return back()->withInput();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Review created.')]);

        return to_route('reviews.index');
    }

    /**
     * Show the form for editing the specified review.
     */
    public function edit(Review $review): Response
    {
        return Inertia::render('reviews/Edit', [
            'review' => $review,
            'products' => $this->productOptions(),
            'statuses' => $this->statusOptions(),
        ]);
    }

    /**
     * Update the specified review.
     */
    public function update(UpdateReviewRequest $request, Review $review, UpdateReviewAction $action): RedirectResponse
    {
        try {
            $action->handle($review, $request->validated());
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not update the review. Please try again.')]);

            return back()->withInput();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Review updated.')]);

        return to_route('reviews.index');
    }

    /**
     * Remove the specified review.
     */
    public function destroy(Review $review, DeleteReviewAction $action): RedirectResponse
    {
        try {
            $action->handle($review);
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not delete the review. Please try again.')]);

            return back();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Review deleted.')]);

        return to_route('reviews.index');
    }

    /**
     * @return array<int, array{value: int, label: string}>
     */
    private function productOptions(): array
    {
        return Product::query()
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn (Product $product) => ['value' => $product->id, 'label' => $product->name])
            ->all();
    }

    /**
     * @return array<int, array{value: string, label: string}>
     */
    private function statusOptions(): array
    {
        return array_map(
            fn (ReviewStatus $case) => ['value' => $case->value, 'label' => $case->label()],
            ReviewStatus::cases(),
        );
    }
}
