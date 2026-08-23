<?php

namespace App\Http\Controllers\Backend;

use App\Actions\Categories\CreateCategoryAction;
use App\Actions\Categories\DeleteCategoryAction;
use App\Actions\Categories\UpdateCategoryAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\StoreCategoryRequest;
use App\Http\Requests\Backend\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class CategoryController extends Controller
{
    /**
     * Display a listing of the categories.
     */
    public function index(): Response
    {
        return Inertia::render('categories/Index', [
            'categories' => Category::query()
                ->with('parent:id,name')
                ->orderBy('position')
                ->paginate(15)
                ->withQueryString(),
        ]);
    }

    /**
     * Show the form for creating a new category.
     */
    public function create(): Response
    {
        return Inertia::render('categories/Create', [
            'parents' => Category::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    /**
     * Store a newly created category.
     */
    public function store(StoreCategoryRequest $request, CreateCategoryAction $action): RedirectResponse
    {
        $data = $request->safe()->except('icon');
        $data['home_status'] = $request->boolean('home_status');

        try {
            $action->handle($data, $request->file('icon'));
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not create the category. Please try again.')]);

            return back()->withInput();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Category created.')]);

        return to_route('categories.index');
    }

    /**
     * Show the form for editing the specified category.
     */
    public function edit(Category $category): Response
    {
        return Inertia::render('categories/Edit', [
            'category' => $category,
            'parents' => Category::query()->whereKeyNot($category->id)->orderBy('name')->get(['id', 'name']),
        ]);
    }

    /**
     * Update the specified category.
     */
    public function update(UpdateCategoryRequest $request, Category $category, UpdateCategoryAction $action): RedirectResponse
    {
        $data = $request->safe()->except('icon');
        $data['home_status'] = $request->boolean('home_status');

        try {
            $action->handle($category, $data, $request->file('icon'));
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not update the category. Please try again.')]);

            return back()->withInput();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Category updated.')]);

        return to_route('categories.index');
    }

    /**
     * Remove the specified category.
     */
    public function destroy(Category $category, DeleteCategoryAction $action): RedirectResponse
    {
        try {
            $action->handle($category);
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not delete the category. Please try again.')]);

            return back();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Category deleted.')]);

        return to_route('categories.index');
    }
}
