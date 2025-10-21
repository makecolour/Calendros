<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startTime = fake()->dateTimeBetween('now', '+1 month');
        $endTime = (clone $startTime)->modify('+' . fake()->numberBetween(1, 4) . ' hours');

        return [
            'calendar_id' => \App\Models\Calendar::factory(),
            'title' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'start_time' => $startTime,
            'end_time' => $endTime,
            'location' => fake()->address(),
            'is_all_day' => false,
        ];
    }

    public function allDay(): static
    {
        return $this->state(function (array $attributes) {
            $startTime = fake()->dateTimeBetween('now', '+1 month');
            return [
                'start_time' => $startTime->setTime(0, 0, 0),
                'end_time' => (clone $startTime)->setTime(23, 59, 59),
                'is_all_day' => true,
            ];
        });
    }
}
