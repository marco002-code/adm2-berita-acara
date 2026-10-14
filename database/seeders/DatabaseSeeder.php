<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(TeacherSeeder::class);
        $this->call(ProctorSeeder::class);
        $this->call(ParticipantSeeder::class);
        $this->call(AttendanceSeeder::class);
        $this->call(MinuteOfEventSeeder::class);

        // Akun Proktor Utama
        User::query()->firstOrCreate(
            ['email' => 'proktor@sekolah.sch.id'],
            [
                'username' => 'proktor',
                'name' => 'Proktor Utama',
                'password' => bcrypt('password'),
                'is_admin' => true,
            ]
        );
    }
}
