<?php

namespace App\Actions\Roles;

use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CreateRoleAction
{
    /**
     * @param  array{name: string, permissions?: array<int, int|string>}  $data
     */
    public function handle(array $data): Role
    {
        return DB::transaction(function () use ($data) {
            $role = Role::create(['name' => $data['name']]);

            $role->syncPermissions(
                Permission::query()->whereKey($data['permissions'] ?? [])->get(),
            );

            return $role;
        });
    }
}
