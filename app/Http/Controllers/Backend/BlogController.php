<?php

namespace App\Http\Controllers\Backend;

use App\Actions\Blogs\CreateBlogAction;
use App\Actions\Blogs\DeleteBlogAction;
use App\Actions\Blogs\UpdateBlogAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\StoreBlogRequest;
use App\Http\Requests\Backend\UpdateBlogRequest;
use App\Models\Blog;
use App\Models\BlogCategory;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class BlogController extends Controller
{
    /**
     * Display a listing of the blogs.
     */
    public function index(): Response
    {
        return Inertia::render('blogs/Index', [
            'blogs' => Blog::query()->with('category:id,name')->latest()->get(),
        ]);
    }

    /**
     * Show the form for creating a new blog.
     */
    public function create(): Response
    {
        return Inertia::render('blogs/Create', [
            'categories' => $this->categoryOptions(),
        ]);
    }

    /**
     * Store a newly created blog.
     */
    public function store(StoreBlogRequest $request, CreateBlogAction $action): RedirectResponse
    {
        $data = $request->safe()->except('image');
        $data['is_published'] = $request->boolean('is_published');

        try {
            $action->handle($data, $request->file('image'));
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not create the blog. Please try again.')]);

            return back()->withInput();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Blog created.')]);

        return to_route('blogs.index');
    }

    /**
     * Show the form for editing the specified blog.
     */
    public function edit(Blog $blog): Response
    {
        return Inertia::render('blogs/Edit', [
            'blog' => $blog,
            'categories' => $this->categoryOptions(),
        ]);
    }

    /**
     * Update the specified blog.
     */
    public function update(UpdateBlogRequest $request, Blog $blog, UpdateBlogAction $action): RedirectResponse
    {
        $data = $request->safe()->except('image');
        $data['is_published'] = $request->boolean('is_published');

        try {
            $action->handle($blog, $data, $request->file('image'));
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not update the blog. Please try again.')]);

            return back()->withInput();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Blog updated.')]);

        return to_route('blogs.index');
    }

    /**
     * Remove the specified blog.
     */
    public function destroy(Blog $blog, DeleteBlogAction $action): RedirectResponse
    {
        try {
            $action->handle($blog);
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not delete the blog. Please try again.')]);

            return back();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Blog deleted.')]);

        return to_route('blogs.index');
    }

    /**
     * @return array<int, array{value: int, label: string}>
     */
    private function categoryOptions(): array
    {
        return BlogCategory::query()
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn (BlogCategory $category) => ['value' => $category->id, 'label' => $category->name])
            ->all();
    }
}
