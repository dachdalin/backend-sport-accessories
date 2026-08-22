<?php

namespace App\Http\Controllers\Backend;

use App\Actions\Roles\CreateRoleAction;
use App\Actions\Roles\DeleteRoleAction;
use App\Actions\Roles\UpdateRoleAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\StoreRoleRequest;
use App\Http\Requests\Backend\UpdateRoleRequest;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Throwable;

class RoleController extends Controller
{
    /**
     * The system role that must always exist and cannot be deleted.
     */
    private const PROTECTED_ROLE = 'admin';

    /**
     * Display a listing of the roles.
     */
    public function index(): Response
    {
        return Inertia::render('roles/Index', [
            'roles' => Role::query()
                ->with('permissions:id,name')
                ->withCount('permissions')
                ->latest()
                ->get(),
            'permissions' => Permission::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    /**
     * Store a newly created role.
     */
    public function store(StoreRoleRequest $request, CreateRoleAction $action): RedirectResponse
    {
        try {
            $action->handle($request->validated());
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not create the role. Please try again.')]);

            return back()->withInput();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Role created.')]);

        return to_route('roles.index');
    }

    /**
     * Update the specified role.
     */
    public function update(UpdateRoleRequest $request, Role $role, UpdateRoleAction $action): RedirectResponse
    {
        try {
            $action->handle($role, $request->validated());
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not update the role. Please try again.')]);

            return back()->withInput();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Role updated.')]);

        return to_route('roles.index');
    }

    /**
     * Remove the specified role.
     */
    public function destroy(Role $role, DeleteRoleAction $action): RedirectResponse
    {
        if ($role->name === self::PROTECTED_ROLE) {
            Inertia::flash('toast', ['type' => 'error', 'message' => __('The admin role is protected and cannot be deleted.')]);

            return back();
        }

        try {
            $action->handle($role);
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not delete the role. Please try again.')]);

            return back();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Role deleted.')]);

        return to_route('roles.index');
    }
}
