<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ManifestFile>
 */
class ManifestFileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'archive_id' => $this->faker->uuid(),
            'description' => $this->faker->sentence(),
            'size' => $this->faker->numberBetween(1024, 10485760), // 1KB to 10MB
            'sha256_tree_hash' => $this->faker->sha256,
            'creation_date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'user_id' => \App\Models\User::factory(),
        ];
    }
}
