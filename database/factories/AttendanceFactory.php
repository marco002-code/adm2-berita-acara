<?php

namespace Database\Factories;

use App\Models\Attendance;
use App\Models\ExamSession;
use App\Models\Participant;
use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Attendance>
 */
class AttendanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'exam_session_id' => ExamSession::factory(),
            'room_id' => Room::factory(),
            'participant_id' => Participant::factory(),
            'status' => 'tidak_hadir',
            'reason' => fake()->randomElement(['Sakit', 'Izin', 'Tanpa Keterangan']),
            'notes' => fake()->sentence(),
        ];
    }
}
