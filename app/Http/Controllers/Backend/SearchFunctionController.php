<?php

namespace App\Http\Controllers\Backend;

use App\Actions\SearchFunctions\CreateSearchFunctionAction;
use App\Actions\SearchFunctions\DeleteSearchFunctionAction;
use App\Actions\SearchFunctions\UpdateSearchFunctionAction;
use App\Enums\SearchFunctionVisibility;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\StoreSearchFunctionRequest;
use App\Http\Requests\Backend\UpdateSearchFunctionRequest;
use App\Models\SearchFunction;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class SearchFunctionController extends Controller
{
    /**
     * Display a listing of the search functions.
     */
    public function index(): Response
    {
        return Inertia::render('search-functions/Index', [
            'searchFunctions' => SearchFunction::query()->latest()->get(),
        ]);
    }

    /**
     * Show the form for creating a new search function.
     */
    public function create(): Response
    {
        return Inertia::render('search-functions/Create', [
            'visibilities' => $this->visibilityOptions(),
        ]);
    }

    /**
     * Store a newly created search function.
     */
    public function store(StoreSearchFunctionRequest $request, CreateSearchFunctionAction $action): RedirectResponse
    {
        try {
            $action->handle($request->validated());
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not create the search function. Please try again.')]);

            return back()->withInput();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Search function created.')]);

        return to_route('search-functions.index');
    }

    /**
     * Show the form for editing the specified search function.
     */
    public function edit(SearchFunction $searchFunction): Response
    {
        return Inertia::render('search-functions/Edit', [
            'searchFunction' => $searchFunction,
            'visibilities' => $this->visibilityOptions(),
        ]);
    }

    /**
     * Update the specified search function.
     */
    public function update(UpdateSearchFunctionRequest $request, SearchFunction $searchFunction, UpdateSearchFunctionAction $action): RedirectResponse
    {
        try {
            $action->handle($searchFunction, $request->validated());
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not update the search function. Please try again.')]);

            return back()->withInput();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Search function updated.')]);

        return to_route('search-functions.index');
    }

    /**
     * Remove the specified search function.
     */
    public function destroy(SearchFunction $searchFunction, DeleteSearchFunctionAction $action): RedirectResponse
    {
        try {
            $action->handle($searchFunction);
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not delete the search function. Please try again.')]);

            return back();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Search function deleted.')]);

        return to_route('search-functions.index');
    }

    /**
     * @return array<int, array{value: string, label: string}>
     */
    private function visibilityOptions(): array
    {
        return array_map(
            fn (SearchFunctionVisibility $case) => ['value' => $case->value, 'label' => $case->label()],
            SearchFunctionVisibility::cases(),
        );
    }
}
