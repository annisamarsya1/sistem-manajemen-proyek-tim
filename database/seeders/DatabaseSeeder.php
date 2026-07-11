<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $now = now();

        DB::table('users')->insert([
            // Admin utama sistem
            [
                'name' => 'Admin Utama',
                'email' => 'admin@proyektim.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            // Project Manager
            [
                'name' => 'Budi PM',
                'email' => 'pm@proyektim.com',
                'password' => Hash::make('password'),
                'role' => 'project_manager',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            // Employee 1
            [
                'name' => 'Employee 1',
                'email' => 'employee1@proyektim.com',
                'password' => Hash::make('password'),
                'role' => 'employee',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            // Employee 2
            [
                'name' => 'Employee 2',
                'email' => 'employee2@proyektim.com',
                'password' => Hash::make('password'),
                'role' => 'employee',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            // Employee 3
            [
                'name' => 'Employee 3',
                'email' => 'employee3@proyektim.com',
                'password' => Hash::make('password'),
                'role' => 'employee',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
