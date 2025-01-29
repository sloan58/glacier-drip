<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure the admin role exists
        $adminRole = Role::firstOrCreate(['name' => 'admin']);

        // Create the admin user
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@glacierdrip.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'),
            ]
        );

        // Assign the admin role to the user
        $adminUser->assignRole($adminRole);

        // Output the admin credentials
        $this->command->info("Admin user created:");
        $this->command->info("Email: admin@example.com");
        $this->command->info("Password: password");
    }
}
