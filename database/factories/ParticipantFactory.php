<?php

namespace Database\Factories;

use App\Models\ExamSession;
use App\Models\Participant;
use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Participant>
 */
class ParticipantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'room_id' => Room::factory(),
            'exam_session_id' => ExamSession::factory(),
            'participant_number' => 'U-'.fake()->unique()->numerify('####-####'),
            'name' => fake()->name(),
            'nisn' => fake()->numerify('##########'),
            'gender' => fake()->randomElement(['L', 'P']),
            'school_origin' => 'SMA Negeri 1 Contoh',
        ];
    }
}
