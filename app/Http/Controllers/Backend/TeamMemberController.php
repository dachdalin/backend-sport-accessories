<?php

namespace App\Http\Controllers\Backend;

use App\Actions\TeamMembers\CreateTeamMemberAction;
use App\Actions\TeamMembers\DeleteTeamMemberAction;
use App\Actions\TeamMembers\UpdateTeamMemberAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\StoreTeamMemberRequest;
use App\Http\Requests\Backend\UpdateTeamMemberRequest;
use App\Models\TeamMember;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class TeamMemberController extends Controller
{
    /**
     * Display a listing of the team members.
     */
    public function index(): Response
    {
        return Inertia::render('team-members/Index', [
            'teamMembers' => TeamMember::query()->orderBy('sort_order')->latest()->get(),
        ]);
    }

    /**
     * Show the form for creating a new team member.
     */
    public function create(): Response
    {
        return Inertia::render('team-members/Create');
    }

    /**
     * Store a newly created team member.
     */
    public function store(StoreTeamMemberRequest $request, CreateTeamMemberAction $action): RedirectResponse
    {
        $data = $request->safe()->except('photo');
        $data['status'] = $request->boolean('status');

        try {
            $action->handle($data, $request->file('photo'));
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not create the team member. Please try again.')]);

            return back()->withInput();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Team member created.')]);

        return to_route('team-members.index');
    }

    /**
     * Show the form for editing the specified team member.
     */
    public function edit(TeamMember $teamMember): Response
    {
        return Inertia::render('team-members/Edit', [
            'teamMember' => $teamMember,
        ]);
    }

    /**
     * Update the specified team member.
     */
    public function update(UpdateTeamMemberRequest $request, TeamMember $teamMember, UpdateTeamMemberAction $action): RedirectResponse
    {
        $data = $request->safe()->except('photo');
        $data['status'] = $request->boolean('status');

        try {
            $action->handle($teamMember, $data, $request->file('photo'));
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not update the team member. Please try again.')]);

            return back()->withInput();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Team member updated.')]);

        return to_route('team-members.index');
    }

    /**
     * Remove the specified team member.
     */
    public function destroy(TeamMember $teamMember, DeleteTeamMemberAction $action): RedirectResponse
    {
        try {
            $action->handle($teamMember);
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not delete the team member. Please try again.')]);

            return back();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Team member deleted.')]);

        return to_route('team-members.index');
    }
}
