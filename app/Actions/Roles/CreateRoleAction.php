<?php

namespace App\Actions\Roles;

use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class CreateRoleAction
{
    /**
     * @param  array{name: string, permissions?: array<int, int>}  $data
     */
    public function handle(array $data): Role
    {
        return DB::transaction(function () use ($data) {
            $role = Role::create(['name' => $data['name']]);

            $role->syncPermissions($data['permissions'] ?? []);

            return $role;
        });
    }
}
