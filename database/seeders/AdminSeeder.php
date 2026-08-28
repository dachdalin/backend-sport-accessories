<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        $admin->syncRoles('admin');

        $manager = User::firstOrCreate(
            ['email' => 'manager@example.com'],
            [
                'name' => 'Manager',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        $manager->syncRoles('manager');

        $catalogManager = User::firstOrCreate(
            ['email' => 'catalog-manager@example.com'],
            [
                'name' => 'Catalog Manager',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        $catalogManager->syncRoles('catalog manager');

        $customerManager = User::firstOrCreate(
            ['email' => 'customer-manager@example.com'],
            [
                'name' => 'Customer Manager',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        $customerManager->syncRoles('customer manager');

        $contentManager = User::firstOrCreate(
            ['email' => 'content-manager@example.com'],
            [
                'name' => 'Content Manager',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        $contentManager->syncRoles('content manager');

        $support = User::firstOrCreate(
            ['email' => 'support@example.com'],
            [
                'name' => 'Support Agent',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        $support->syncRoles('support');
    }
}
