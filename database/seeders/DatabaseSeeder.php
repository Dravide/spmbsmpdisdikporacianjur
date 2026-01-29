<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Admin User
        User::create([
            'name' => 'Administrator',
            'username' => 'admin',
            'email' => 'admin@disdikpora.go.id',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        // Create Operator SD User
        User::create([
            'name' => 'Operator SD',
            'username' => 'opsd',
            'email' => 'opsd@disdikpora.go.id',
            'password' => bcrypt('password'),
            'role' => 'opsd',
            'is_active' => true,
        ]);

        // Create Operator SMP User
        User::create([
            'name' => 'Operator SMP',
            'username' => 'opsmp',
            'email' => 'opsmp@disdikpora.go.id',
            'password' => bcrypt('password'),
            'role' => 'opsmp',
            'is_active' => true,
        ]);

        // Create CMB User
        User::create([
            'name' => 'Calon Murid',
            'username' => 'cmb001',
            'email' => 'cmb001@example.com',
            'password' => bcrypt('password'),
            'role' => 'cmb',
            'is_active' => true,
        ]);
    }
}
