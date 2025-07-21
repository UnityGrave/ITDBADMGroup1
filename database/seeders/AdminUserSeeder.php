<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Find roles by name
        $adminRole = Role::where('name', 'Admin')->first();
        $customerRole = Role::where('name', 'Customer')->first();

        // Create admin user
        $admin = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('admin1234'),
                'email_verified_at' => now(),
            ]
        );
        if ($adminRole) {
            $admin->roles()->syncWithoutDetaching([$adminRole->id]);
        }

        // Create regular user
        $user = User::updateOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'Regular User',
                'password' => Hash::make('user1234'),
                'email_verified_at' => now(),
            ]
        );
        if ($customerRole) {
            $user->roles()->syncWithoutDetaching([$customerRole->id]);
        }
    }
} 