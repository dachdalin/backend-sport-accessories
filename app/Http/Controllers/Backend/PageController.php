<?php

namespace App\Http\Controllers\Backend;

use App\Actions\Pages\CreatePageAction;
use App\Actions\Pages\DeletePageAction;
use App\Actions\Pages\UpdatePageAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\StorePageRequest;
use App\Http\Requests\Backend\UpdatePageRequest;
use App\Models\Page;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class PageController extends Controller
{
    /**
     * Display a listing of the pages.
     */
    public function index(): Response
    {
        return Inertia::render('pages/Index', [
            'pages' => Page::query()->latest()->get(),
        ]);
    }

    /**
     * Show the form for creating a new page.
     */
    public function create(): Response
    {
        return Inertia::render('pages/Create');
    }

    /**
     * Store a newly created page.
     */
    public function store(StorePageRequest $request, CreatePageAction $action): RedirectResponse
    {
        $data = $request->validated();
        $data['status'] = $request->boolean('status');

        try {
            $action->handle($data);
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not create the page. Please try again.')]);

            return back()->withInput();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Page created.')]);

        return to_route('pages.index');
    }

    /**
     * Show the form for editing the specified page.
     */
    public function edit(Page $page): Response
    {
        return Inertia::render('pages/Edit', [
            'page' => $page,
        ]);
    }

    /**
     * Update the specified page.
     */
    public function update(UpdatePageRequest $request, Page $page, UpdatePageAction $action): RedirectResponse
    {
        $data = $request->validated();
        $data['status'] = $request->boolean('status');

        try {
            $action->handle($page, $data);
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not update the page. Please try again.')]);

            return back()->withInput();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Page updated.')]);

        return to_route('pages.index');
    }

    /**
     * Remove the specified page.
     */
    public function destroy(Page $page, DeletePageAction $action): RedirectResponse
    {
        try {
            $action->handle($page);
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not delete the page. Please try again.')]);

            return back();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Page deleted.')]);

        return to_route('pages.index');
    }
}
