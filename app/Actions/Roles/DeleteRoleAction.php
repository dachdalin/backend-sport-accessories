<?php

namespace App\Actions\Roles;

use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class DeleteRoleAction
{
    public function handle(Role $role): void
    {
        DB::transaction(function () use ($role) {
            $role->delete();
        });
    }
}
