<?php

namespace App\Actions\Users;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class UpdateUserAction
{
    /**
     * @param  array{name: string, email: string, password?: ?string, status?: bool, roles?: array<int, int>}  $data
     */
    public function handle(User $user, array $data): User
    {
        return DB::transaction(function () use ($user, $data) {
            $user->name = $data['name'];
            $user->email = $data['email'];
            $user->status = $data['status'] ?? $user->status;

            if (! empty($data['password'])) {
                $user->password = $data['password'];
            }

            $user->save();

            $user->syncRoles(array_map('intval', $data['roles'] ?? []));

            return $user;
        });
    }
}
