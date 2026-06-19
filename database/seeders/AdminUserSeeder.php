<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Seed an admin user for initial access.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@proyek.test'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );

        User::firstOrCreate(
            ['email' => 'pm@proyek.test'],
            [
                'name' => 'Project Manager',
                'password' => Hash::make('password'),
                'role' => 'project_manager',
                'is_active' => true,
            ]
        );

        User::firstOrCreate(
            ['email' => 'employee@proyek.test'],
            [
                'name' => 'Employee User',
                'password' => Hash::make('password'),
                'role' => 'employee',
                'is_active' => true,
            ]
        );
    }
}
