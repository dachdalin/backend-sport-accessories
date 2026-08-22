<?php

namespace App\Actions\Users;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class CreateUserAction
{
    /**
     * @param  array{name: string, email: string, password: string, roles?: array<int, int>}  $data
     */
    public function handle(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'],
            ]);

            $user->forceFill(['email_verified_at' => now()])->save();

            $user->syncRoles($data['roles'] ?? []);

            return $user;
        });
    }
}
