<?php

namespace Database\Factories;

use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Room>
 */
class RoomFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'Ruang '.fake()->unique()->numberBetween(1, 20),
            'code' => 'R-'.fake()->unique()->numerify('###'),
            'capacity' => 30,
            'description' => 'Laboratorium Komputer',
        ];
    }
}
