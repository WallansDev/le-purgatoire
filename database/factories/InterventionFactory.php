<?php

namespace Database\Factories;

use App\Models\Technician;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Intervention>
 */
class InterventionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $scheduled = Carbon::instance($this->faker->dateTimeBetween('-2 months', '+2 months'));
        $startOffset = $this->faker->numberBetween(-15, 120);
        $started = (clone $scheduled)->addMinutes($startOffset);
        $finished = (clone $started)->addMinutes($this->faker->numberBetween(30, 180));
        $delay = max(0, $scheduled->diffInMinutes($started, false));

        return [
            'technician_id' => Technician::factory(),
            'scheduled_at' => $scheduled,
            'started_at' => $started,
            'finished_at' => $finished,
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->paragraph(),
            'note' => $this->faker->optional(0.7)->numberBetween(1, 5),
            'service_note' => $this->faker->optional(0.7)->numberBetween(0, 5),
            'delay_minutes' => $delay,
            'was_late' => $delay > 0,
        ];
    }
}
