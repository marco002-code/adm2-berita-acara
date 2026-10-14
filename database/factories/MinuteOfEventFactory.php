<?php

namespace Database\Factories;

use App\Models\ExamSession;
use App\Models\MinuteOfEvent;
use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MinuteOfEvent>
 */
class MinuteOfEventFactory extends Factory
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
            'exam_date' => now()->toDateString(),
            'proctor_name' => fake()->name(),
            'proctor_nip' => fake()->numerify('19850101########'),
            'supervisor_name' => fake()->name(),
            'supervisor_nip' => fake()->numerify('19820202########'),
            'headmaster_name' => fake()->name(),
            'headmaster_nip' => fake()->numerify('19750303########'),
            'incident_notes' => 'Ujian berjalan tertib dan lancar tanpa kendala teknis.',
            'status' => 'submitted',
        ];
    }
}
