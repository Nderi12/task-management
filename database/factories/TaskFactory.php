<?php

namespace Database\Factories;

use App\Enums\TaskStatus;
use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => rtrim(fake()->sentence(4), '.'),
            'description' => fake()->boolean(70) ? fake()->paragraph() : null,
            'status' => fake()->randomElement(TaskStatus::cases()),
            'position' => 0,
        ];
    }

    public function pending(): static
    {
        return $this->state(['status' => TaskStatus::Pending]);
    }

    public function completed(): static
    {
        return $this->state(['status' => TaskStatus::Completed]);
    }
}
