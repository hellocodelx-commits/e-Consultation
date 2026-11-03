<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $demoAccounts = [
            ['name' => 'System Administrator', 'email' => 'admin@example.com', 'role' => User::ROLE_ADMIN],
            ['name' => 'Dr. Amara Okafor', 'email' => 'staff@example.com', 'role' => User::ROLE_STAFF],
            ['name' => 'Chidi Eze', 'email' => 'patient@example.com', 'role' => User::ROLE_PATIENT],
        ];

        foreach ($demoAccounts as $account) {
            User::updateOrCreate(
                ['email' => $account['email']],
                $account + ['password' => 'password', 'email_verified_at' => now()],
            );
        }
    }
}
