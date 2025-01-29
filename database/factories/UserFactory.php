<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn(array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /*
     * A user that has completed onboarding
     */
    public function onboarded(): Factory|UserFactory
    {
        return $this->state(fn(array $attributes) => [
            'aws_access_key_id' => 'new-access-key',
            'aws_secret_access_key' => 'new-secret-key',
            'aws_region' => 'eu-west-1',
            'aws_s3_bucket' => 'test-bucket',
        ]);
    }

    /*
     * A user with admin role
     */
    public function admin(): Factory|UserFactory
    {
        return $this->afterCreating(function (User $user) {
            $role = Role::firstOrCreate(['name' => 'admin']);
            $user->assignRole($role);
        });
    }
}
