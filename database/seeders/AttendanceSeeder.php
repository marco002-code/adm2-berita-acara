<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\ExamSession;
use App\Models\Participant;
use Illuminate\Database\Seeder;

class AttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 11 siswa yang tidak hadir pada ujian Matematika
        $absentList = [
            'P0100320017' => ['name' => 'IQBAL RADITYA ARYASATYA', 'reason' => 'Tanpa Keterangan', 'notes' => 'Belum Mengerjakan'],
            'P0100320247' => ['name' => 'WAHYU SEKAR KIRANA', 'reason' => 'Tanpa Keterangan', 'notes' => 'Belum Mengerjakan'],
            'P0100320185' => ['name' => 'WILDHAN NUR ARIFIN', 'reason' => 'Tanpa Keterangan', 'notes' => 'Belum Mengerjakan'],
            'P0100320007' => ['name' => 'ARKAN ARROYYAN', 'reason' => 'Tanpa Keterangan', 'notes' => 'Belum Mengerjakan'],
            'P0100320235' => ['name' => 'LATHIIFA FARAA AZZAHRA', 'reason' => 'Tanpa Keterangan', 'notes' => 'Belum Mengerjakan'],
            'P0100320053' => ['name' => 'MUHAMMAD HASBY NAUFAL PRATAMA', 'reason' => 'Tanpa Keterangan', 'notes' => 'Belum Mengerjakan'],
            'P0100320001' => ['name' => 'ABIGAIL XAVIOLA NUGRAHA', 'reason' => 'Tanpa Keterangan', 'notes' => 'Belum Mengerjakan'],
            'P0100320086' => ['name' => 'REYHAN ALFATIR ARSYA PRATISTA', 'reason' => 'Tanpa Keterangan', 'notes' => 'Belum Mengerjakan'],
            'P0100320184' => ['name' => 'WAWA RAHMADANI', 'reason' => 'Tanpa Keterangan', 'notes' => 'Belum Mengerjakan'],
            'P0100320080' => ['name' => 'KIANNO LUCKY ALEANDA', 'reason' => 'Tanpa Keterangan', 'notes' => 'Belum Mengerjakan'],
            'P0100320144' => ['name' => 'MUHAMMAD RAYYAN KHAIRUL AKRAM', 'reason' => 'Tanpa Keterangan', 'notes' => 'Belum Mengerjakan'],
        ];

        // Ambil sesi ujian Matematika (12 Oktober 2026 / 01 Matematika TKA)
        $mathSessionIds = ExamSession::query()
            ->where('subject', 'like', '%Matematika%')
            ->pluck('id');

        $count = 0;
        foreach ($absentList as $nis => $info) {
            $participant = Participant::query()
                ->where('participant_number', $nis)
                ->whereIn('exam_session_id', $mathSessionIds)
                ->first();

            if ($participant) {
                Attendance::updateOrCreate(
                    [
                        'exam_session_id' => $participant->exam_session_id,
                        'participant_id' => $participant->id,
                    ],
                    [
                        'room_id' => $participant->room_id,
                        'status' => 'tidak_hadir',
                        'reason' => $info['reason'],
                        'notes' => $info['notes'],
                    ]
                );
                $count++;
            }
        }

        $this->command?->info("Berhasil mencatat {$count} siswa tidak hadir pada ujian Matematika.");
    }
}
