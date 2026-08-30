<?php

namespace App\Http\Controllers\Backend;

use App\Actions\Users\CreateUserAction;
use App\Actions\Users\DeleteUserAction;
use App\Actions\Users\UpdateUserAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\StoreUserRequest;
use App\Http\Requests\Backend\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;
use Throwable;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index(): Response
    {
        return Inertia::render('users/Index', [
            'users' => User::with('roles:id,name')->latest()->paginate(15)->withQueryString(),
        ]);
    }

    /**
     * Show the form for creating a new user.
     */
    public function create(): Response
    {
        return Inertia::render('users/Create', [
            'roles' => Role::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    /**
     * Store a newly created user.
     */
    public function store(StoreUserRequest $request, CreateUserAction $action): RedirectResponse
    {
        $data = $request->validated();
        $data['status'] = $request->boolean('status', true);

        try {
            $action->handle($data);
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not create the user. Please try again.')]);

            return back()->withInput();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('User created.')]);

        return to_route('users.index');
    }

    /**
     * Display the specified user.
     */
    public function show(User $user): Response
    {
        return Inertia::render('users/Show', [
            'user' => $user->load('roles:id,name'),
        ]);
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user): Response
    {
        return Inertia::render('users/Edit', [
            'user' => $user->load('roles:id,name'),
            'roles' => Role::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    /**
     * Update the specified user.
     */
    public function update(UpdateUserRequest $request, User $user, UpdateUserAction $action): RedirectResponse
    {
        $data = $request->validated();
        $data['status'] = $request->boolean('status');

        if ($user->is($request->user()) && ! $data['status']) {
            Inertia::flash('toast', ['type' => 'error', 'message' => __('You cannot ban your own account.')]);

            return back();
        }

        try {
            $action->handle($user, $data);
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not update the user. Please try again.')]);

            return back()->withInput();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('User updated.')]);

        return to_route('users.index');
    }

    /**
     * Remove the specified user.
     */
    public function destroy(Request $request, User $user, DeleteUserAction $action): RedirectResponse
    {
        if ($user->is($request->user())) {
            Inertia::flash('toast', ['type' => 'error', 'message' => __('You cannot delete your own account.')]);

            return back();
        }

        try {
            $action->handle($user);
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not delete the user. Please try again.')]);

            return back();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('User deleted.')]);

        return to_route('users.index');
    }
}
