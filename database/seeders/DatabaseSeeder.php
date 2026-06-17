<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // Seed Admin User
        User::forceCreate([
            'name' => 'Admin Utama',
            'email' => 'admin@proyektim.com',
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        // Seed Project Manager User
        User::forceCreate([
            'name' => 'Budi PM',
            'email' => 'pm@proyektim.com',
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
            'role' => 'project_manager',
            'is_active' => true,
        ]);

        // Seed Employee Users
        for ($i = 1; $i <= 3; $i++) {
            User::forceCreate([
                'name' => "Employee {$i}",
                'email' => "employee{$i}@proyektim.com",
                'email_verified_at' => now(),
                'password' => bcrypt('password'),
                'role' => 'employee',
                'is_active' => true,
            ]);
        }
    }
}
