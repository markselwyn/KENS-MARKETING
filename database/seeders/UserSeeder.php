<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create the System Administrator
        User::updateOrCreate(
            ['email' => 'admin@kensmarketing.com'],
            [
                'name' => 'Store Manager',
                'password' => Hash::make('password123'), // Securely hashed password
                'role' => 'admin',
            ]
        );

        // 2. Create the Staff Member
        User::updateOrCreate(
            ['email' => 'staff@kensmarketing.com'],
            [
                'name' => 'Warehouse Custodian',
                'password' => Hash::make('password123'),
                'role' => 'staff',
            ]
        );
    }
}