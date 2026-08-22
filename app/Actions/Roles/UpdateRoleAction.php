<?php

namespace App\Actions\Roles;

use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class UpdateRoleAction
{
    /**
     * @param  array{name: string, permissions?: array<int, int>}  $data
     */
    public function handle(Role $role, array $data): Role
    {
        DB::transaction(function () use ($role, $data) {
            $role->update(['name' => $data['name']]);

            $role->syncPermissions($data['permissions'] ?? []);
        });

        return $role;
    }
}
