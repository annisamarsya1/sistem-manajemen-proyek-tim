<?php

namespace Database\Factories;

use App\Models\TeamProject;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TeamProject>
 */
class TeamProjectFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'client_name' => fake()->company(),
            'start_date' => fake()->dateTimeBetween('-3 months', 'now'),
            'deadline' => fake()->dateTimeBetween('now', '+6 months'),
            'budget' => fake()->randomFloat(2, 1000000, 50000000),
            'priority' => fake()->randomElement(['low', 'medium', 'high', 'urgent']),
            'status' => fake()->randomElement(['planning', 'active', 'on_hold', 'completed', 'cancelled']),
            'created_by' => User::factory(),
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'active']);
    }
}
