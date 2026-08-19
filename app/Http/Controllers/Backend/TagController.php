<?php

namespace App\Http\Controllers\Backend;

use App\Actions\Tags\CreateTagAction;
use App\Actions\Tags\DeleteTagAction;
use App\Actions\Tags\UpdateTagAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\StoreTagRequest;
use App\Http\Requests\Backend\UpdateTagRequest;
use App\Models\Tag;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class TagController extends Controller
{
    /**
     * Display a listing of the tags.
     */
    public function index(): Response
    {
        return Inertia::render('tags/Index', [
            'tags' => Tag::query()->latest()->get(),
        ]);
    }

    /**
     * Store a newly created tag.
     */
    public function store(StoreTagRequest $request, CreateTagAction $action): RedirectResponse
    {
        try {
            $action->handle($request->validated());
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not create the tag. Please try again.')]);

            return back()->withInput();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Tag created.')]);

        return to_route('tags.index');
    }

    /**
     * Update the specified tag.
     */
    public function update(UpdateTagRequest $request, Tag $tag, UpdateTagAction $action): RedirectResponse
    {
        try {
            $action->handle($tag, $request->validated());
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not update the tag. Please try again.')]);

            return back()->withInput();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Tag updated.')]);

        return to_route('tags.index');
    }

    /**
     * Remove the specified tag.
     */
    public function destroy(Tag $tag, DeleteTagAction $action): RedirectResponse
    {
        try {
            $action->handle($tag);
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not delete the tag. Please try again.')]);

            return back();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Tag deleted.')]);

        return to_route('tags.index');
    }
}
