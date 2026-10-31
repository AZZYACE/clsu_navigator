<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class DatabaseSeeder extends Seeder
{
    // Creates a first Super Admin and a test Student so login can be tried.
    // Roles come from clsu_nav_new_db.sql. Run again to reset these passwords.
    public function run(): void
    {
        $password = env('SEED_PASSWORD');

        if (!$password) {
            throw new RuntimeException('Add SEED_PASSWORD=YourPasswordHere to your .env file, then run the seeder again.');
        }

        $accounts = [
            ['Super Admin', 'CLSU Super Admin', 'superadmin@clsu-navigator.test'],
            ['Student', 'Test Student', 'student@clsu-navigator.test'],
        ];

        foreach ($accounts as [$roleName, $fullName, $email]) {
            $roleId = DB::table('roles')->where('role_name', $roleName)->value('role_id');

            if (!$roleId) {
                throw new RuntimeException("Role '{$roleName}' not found. Import clsu_nav_new_db.sql first.");
            }

            User::updateOrCreate(
                ['email' => $email],
                [
                    'role_id' => $roleId,
                    'full_name' => $fullName,
                    'password_hash' => Hash::make($password),
                    'account_status' => 'Active',
                    'email_verified_at' => now(),
                ]
            );

            $this->command->info("Ready: {$email} ({$roleName})");
        }
    }
}
