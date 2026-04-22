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
                'name'     => 'Admin User',
                'email'    => 'admin@makazilink.co.ke',
                'password' => Hash::make('password'),
                'role'     => 'admin',
                'phone'    => '0700000001',
            ],
            [
                'name'     => 'Agent User',
                'email'    => 'agent@makazilink.co.ke',
                'password' => Hash::make('password'),
                'role'     => 'agent',
                'phone'    => '0700000002',
            ],
            [
                'name'     => 'Accountant User',
                'email'    => 'accountant@makazilink.co.ke',
                'password' => Hash::make('password'),
                'role'     => 'accountant',
                'phone'    => '0700000003',
            ],
            [
                'name'     => 'Caretaker User',
                'email'    => 'caretaker@makazilink.co.ke',
                'password' => Hash::make('password'),
                'role'     => 'caretaker',
                'phone'    => '0700000004',
            ],
            [
                'name'     => 'Tenant User',
                'email'    => 'tenant@makazilink.co.ke',
                'password' => Hash::make('password'),
                'role'     => 'tenant',
                'phone'    => '0700000005',
            ],
        ];

        foreach ($users as $userData) {
            User::firstOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }
    }
}