<?php

namespace App\Http\Controllers\Backend;

use App\Actions\BlogCategories\CreateBlogCategoryAction;
use App\Actions\BlogCategories\DeleteBlogCategoryAction;
use App\Actions\BlogCategories\UpdateBlogCategoryAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\StoreBlogCategoryRequest;
use App\Http\Requests\Backend\UpdateBlogCategoryRequest;
use App\Models\BlogCategory;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class BlogCategoryController extends Controller
{
    /**
     * Display a listing of the blog categories.
     */
    public function index(): Response
    {
        return Inertia::render('blog-categories/Index', [
            'blogCategories' => BlogCategory::query()->latest()->get(),
        ]);
    }

    /**
     * Store a newly created blog category.
     */
    public function store(StoreBlogCategoryRequest $request, CreateBlogCategoryAction $action): RedirectResponse
    {
        try {
            $action->handle($request->validated());
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not create the blog category. Please try again.')]);

            return back()->withInput();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Blog category created.')]);

        return to_route('blog-categories.index');
    }

    /**
     * Update the specified blog category.
     */
    public function update(UpdateBlogCategoryRequest $request, BlogCategory $blogCategory, UpdateBlogCategoryAction $action): RedirectResponse
    {
        try {
            $action->handle($blogCategory, $request->validated());
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not update the blog category. Please try again.')]);

            return back()->withInput();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Blog category updated.')]);

        return to_route('blog-categories.index');
    }

    /**
     * Remove the specified blog category.
     */
    public function destroy(BlogCategory $blogCategory, DeleteBlogCategoryAction $action): RedirectResponse
    {
        try {
            $action->handle($blogCategory);
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not delete the blog category. Please try again.')]);

            return back();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Blog category deleted.')]);

        return to_route('blog-categories.index');
    }
}
