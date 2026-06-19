<?php

namespace Database\Factories;

use App\Models\TeamProject;
use App\Models\TimeLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<TimeLog>
 */
class TimeLogFactory extends Factory
{
    public function definition(): array
    {
        $startTime = fake()->dateTimeBetween('-7 days', 'now');
        $endTime = (clone $startTime)->modify('+'.fake()->numberBetween(1, 8).' hours');
        $durationHours = round(($endTime->getTimestamp() - $startTime->getTimestamp()) / 3600, 2);

        return [
            'user_id' => User::factory(),
            'project_id' => TeamProject::factory(),
            'task_id' => null,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'duration_hours' => $durationHours,
            'notes' => fake()->optional()->sentence(),
            'status' => 'pending',
            'reviewed_by' => null,
            'reviewed_at' => null,
        ];
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
            'reviewed_by' => User::factory()->create(['role' => 'admin'])->id,
            'reviewed_at' => now(),
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'rejected',
            'reviewed_by' => User::factory()->create(['role' => 'admin'])->id,
            'reviewed_at' => now(),
        ]);
    }

    public function today(): static
    {
        return $this->state(function (array $attributes) {
            $startTime = Carbon::today()->addHours(fake()->numberBetween(8, 12));
            $endTime = $startTime->copy()->addHours(fake()->numberBetween(1, 4));

            return [
                'start_time' => $startTime,
                'end_time' => $endTime,
                'duration_hours' => round($endTime->diffInSeconds($startTime) / 3600, 2),
            ];
        });
    }
}
