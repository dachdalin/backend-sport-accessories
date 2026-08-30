<?php

namespace App\Actions\Users;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class CreateUserAction
{
    /**
     * @param  array{name: string, email: string, password: string, status?: bool, roles?: array<int, int>}  $data
     */
    public function handle(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'],
                'status' => $data['status'] ?? true,
            ]);

            $user->forceFill(['email_verified_at' => now()])->save();

            $user->syncRoles(array_map('intval', $data['roles'] ?? []));

            return $user;
        });
    }
}
