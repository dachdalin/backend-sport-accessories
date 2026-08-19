<?php

namespace App\Http\Controllers\Backend;

use App\Actions\HelpTopics\CreateHelpTopicAction;
use App\Actions\HelpTopics\DeleteHelpTopicAction;
use App\Actions\HelpTopics\UpdateHelpTopicAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\StoreHelpTopicRequest;
use App\Http\Requests\Backend\UpdateHelpTopicRequest;
use App\Models\HelpTopic;
use App\Services\HelpTopicService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class HelpTopicController extends Controller
{
    public function __construct(private HelpTopicService $helpTopics) {}

    /**
     * Display a listing of the help topics.
     */
    public function index(): Response
    {
        return Inertia::render('help-topics/Index', [
            'helpTopics' => $this->helpTopics->list(),
        ]);
    }

    /**
     * Show the form for creating a new help topic.
     */
    public function create(): Response
    {
        return Inertia::render('help-topics/Create');
    }

    /**
     * Store a newly created help topic.
     */
    public function store(StoreHelpTopicRequest $request, CreateHelpTopicAction $action): RedirectResponse
    {
        $action->handle($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Help topic created.')]);

        return to_route('help-topics.index');
    }

    /**
     * Show the form for editing the specified help topic.
     */
    public function edit(HelpTopic $helpTopic): Response
    {
        return Inertia::render('help-topics/Edit', [
            'helpTopic' => $helpTopic,
        ]);
    }

    /**
     * Update the specified help topic.
     */
    public function update(UpdateHelpTopicRequest $request, HelpTopic $helpTopic, UpdateHelpTopicAction $action): RedirectResponse
    {
        $action->handle($helpTopic, $request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Help topic updated.')]);

        return to_route('help-topics.index');
    }

    /**
     * Remove the specified help topic.
     */
    public function destroy(HelpTopic $helpTopic, DeleteHelpTopicAction $action): RedirectResponse
    {
        $action->handle($helpTopic);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Help topic deleted.')]);

        return to_route('help-topics.index');
    }
}
