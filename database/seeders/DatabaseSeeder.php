<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CategorySeeder::class,
            RaritySeeder::class,
            SetSeeder::class,
            RoleSeeder::class,
            AdminUserSeeder::class,
        ]);

        // Create test users
        $adminUser = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@konibui.com',
        ]);

        $employeeUser = User::factory()->create([
            'name' => 'Employee User',
            'email' => 'employee@konibui.com',
        ]);

        $customerUser = User::factory()->create([
            'name' => 'Test Customer',
            'email' => 'test@example.com',
        ]);

        // Assign roles to test users
        $adminUser->assignRole('Admin');
        $employeeUser->assignRole('Employee');
        $customerUser->assignRole('Customer');

        $this->command->info('Database seeded with roles and test users');
    }
}
