<?php

namespace Database\Seeders;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userRole = Role::firstOrCreate(['name' => 'user']);

        $user = User::factory()->create();

        $user->assignRole($userRole);

        // Output the user credentials
        $this->command->info("Standard user created:");
        $this->command->info("Email: {$user->email}");
        $this->command->info("Password: password");
    }
}
