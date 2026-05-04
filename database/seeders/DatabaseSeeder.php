<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name'      => 'Admin User',
                'email'     => 'admin@makazilink.co.ke',
                'password'  => Hash::make('password'),
                'role'      => 'admin',
                'phone'     => '0700000001',
                'is_active' => true,
            ],
            [
                'name'      => 'Agent User',
                'email'     => 'agent@makazilink.co.ke',
                'password'  => Hash::make('password'),
                'role'      => 'agent',
                'phone'     => '0700000002',
                'is_active' => true,
            ],
            [
                'name'      => 'Accountant User',
                'email'     => 'accountant@makazilink.co.ke',
                'password'  => Hash::make('password'),
                'role'      => 'accountant',
                'phone'     => '0700000003',
                'is_active' => true,
            ],
            [
                'name'      => 'Caretaker User',
                'email'     => 'caretaker@makazilink.co.ke',
                'password'  => Hash::make('password'),
                'role'      => 'caretaker',
                'phone'     => '0700000004',
                'is_active' => true,
            ],
            [
                'name'      => 'Tenant User',
                'email'     => 'tenant@makazilink.co.ke',
                'password'  => Hash::make('password'),
                'role'      => 'tenant',
                'phone'     => '0700000005',
                'is_active' => true,
            ],
            [
                'name'      => 'Super Admin',
                'email'     => 'superadmin@makazilink.co.ke',
                'password'  => Hash::make('superadmin@2026#secure'),
                'role'      => 'superadmin',
                'phone'     => '0700000000',
                'is_active' => true,
            ],
        ];

        foreach ($users as $userData) {
            User::firstOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }

        $this->command->info('✅ Default users created successfully.');
        $this->command->info('Admin: admin@makazilink.co.ke / password');
        $this->command->info('Superadmin: superadmin@makazilink.co.ke / superadmin@2026#secure');
    }
}