<?php

namespace Database\Factories;

use App\Models\ExamSession;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ExamSession>
 */
class ExamSessionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'Sesi '.fake()->numberBetween(1, 3),
            'exam_date' => now()->toDateString(),
            'start_time' => '07:30',
            'end_time' => '09:30',
            'subject' => fake()->randomElement(['Literasi Membaca', 'Numerasi', 'Survei Karakter']),
            'description' => 'Asesmen Nasional Berbasis Komputer',
        ];
    }
}
