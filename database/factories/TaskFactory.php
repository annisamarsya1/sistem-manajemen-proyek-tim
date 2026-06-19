<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\TeamProject;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
{
    public function definition(): array
    {
        return [
            'project_id' => TeamProject::factory(),
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'assignee_id' => User::factory(),
            'priority' => fake()->randomElement(['low', 'medium', 'high']),
            'start_date' => fake()->dateTimeBetween('-1 month', 'now'),
            'due_date' => fake()->dateTimeBetween('now', '+1 month'),
            'progress_percent' => fake()->randomFloat(2, 0, 100),
            'status' => fake()->randomElement(['todo', 'in_progress', 'review', 'done']),
            'completed_at' => null,
        ];
    }
}
